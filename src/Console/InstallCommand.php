<?php

namespace khaliqueahmed\LocalAI\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'ai:install';
    protected $description = 'Configure permission execution blocks for the embedded C++ AI binaries';

    public function handle()
    {
        $os = PHP_OS_FAMILY;
        $this->info("System identified as: {$os}. Preparing binaries...");

        $binaryPath = base_path('vendor/khaliqueahmed/laravel-local-ai/bin/' . strtolower($os) . '-x64/llama-cli');

        if ($os !== 'Windows') {
            // Remove Unix lock execution flags silently
            chmod($binaryPath, 0755);
        }

        $this->info("✅ Standalone C++ AI Engine is mapped and ready!");
    }
}