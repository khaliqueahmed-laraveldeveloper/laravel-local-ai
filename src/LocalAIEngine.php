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
        $context = (int) config('local-ai.context', 512);

        $command = [
            $this->binaryPath,
            '-m', $this->modelPath,
            '-p', $prompt,
            '-n', (string)$maxTokens,
            '-t', (string) max(1, $threads),
            '-c', (string) max(1, $context),
            '--single-turn',
            '--no-mmap',
            '--log-disable',
            '--no-display-prompt'
        ];


        // Use Symfony Process so we can disable the default 60s timeout
        $process = new SymfonyProcess($command);

        // disable timeout for long-running model execution
        $process->setTimeout(null);
       
        $output='';
        
        $process->run(function($type,$buffer) use (&$output){
            $output.=$buffer;
        });


        if (!$process->isSuccessful()) {
            throw new Exception("Local Core Processing Error: " . $process->getErrorOutput());
        }
        
        $rawOutput = trim($output);


        if (str_contains($rawOutput, '> ')) {
            $parts = explode('> ', $rawOutput, 2);
            $promptAndAnswer = end($parts); 
        } else {
            $promptAndAnswer = $rawOutput;
        }


        $promptLength = strlen($prompt);


        $onlyAnswer = substr($promptAndAnswer, $promptLength + 50);

        $onlyAnswer = preg_replace('/\[\s*Prompt:.*$/is', '', $onlyAnswer);
        $onlyAnswer = str_replace('Exiting...', '', $onlyAnswer);


        return trim($onlyAnswer);


    }
}