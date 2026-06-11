<?php

namespace khaliqueahmed\LocalAI;

use Symfony\Component\Process\Process as SymfonyProcess;
use Exception;

class LocalAIEngine
{
    protected string $binaryPath;
    protected string $modelPath;

    public function __construct()
    {
        $os = strtolower(PHP_OS_FAMILY);
        $this->binaryPath = base_path("vendor/khaliqueahmed-laraveldeveloper/laravel-local-ai/bin/{$os}-x64/llama-cli" . ($os === 'windows' ? '.exe' : ''));
        $this->modelPath = storage_path(config('local-ai.model_path'));
    }

    public function generate(string $prompt, int $maxTokens = 100): string
    {
        if (!file_exists($this->modelPath)) {
            throw new Exception("Local model (.gguf) file not found at: {$this->modelPath}. Please place your model file there.");
        }

        $threads = (int) config('local-ai.threads', 1);

        // Use Symfony Process so we can disable the default 60s timeout
        $process = new SymfonyProcess([
            $this->binaryPath,
            '-m', $this->modelPath,
            '-p', $prompt,
            '-n', (string)$maxTokens,
            '-t', (string) max(1, $threads),
        ]);

        // disable timeout for long-running model execution
        $process->setTimeout(null);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new Exception("Local Core Processing Error: " . $process->getErrorOutput());
        }

        return trim($process->getOutput());
    }
}