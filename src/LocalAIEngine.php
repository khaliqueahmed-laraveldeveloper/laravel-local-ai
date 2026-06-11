<?php

namespace khaliqueahmed\LocalAI;

use Illuminate\Support\Facades\Process;
use Exception;

class LocalAIEngine
{
    protected string $binaryPath;
    protected string $modelPath;

    public function __construct()
    {
        $os = strtolower(PHP_OS_FAMILY);
        $this->binaryPath = base_path("vendor/khaliqueahmed/laravel-local-ai/bin/{$os}-x64/llama-cli" . ($os === 'windows' ? '.exe' : ''));
        $this->modelPath = storage_path(config('local-ai.model_path'));
    }

    public function generate(string $prompt, int $maxTokens = 100): string
    {
        if (!file_exists($this->modelPath)) {
            throw new Exception("Local model (.gguf) file not found at: {$this->modelPath}. Please place your model file there.");
        }

        // Native background process execution via server CPU/GPU streams
        $result = Process::run([
            $this->binaryPath,
            '-m', $this->modelPath,
            '-p', $prompt,
            '-n', (string)$maxTokens,
            '--quiet'
        ]);

        if ($result->failed()) {
            throw new Exception("Local Core Processing Error: " . $result->errorOutput());
        }

        return trim($result->output());
    }
}