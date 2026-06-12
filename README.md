# Laravel Local AI

A simple Laravel package to run a local LLM binary from a Laravel application.

## Install

1. Add the package to your Laravel app via composer.
2. Publish config if needed:

```bash
php artisan vendor:publish --provider="khaliqueahmed\LocalAI\LocalAIServiceProvider"
```

3. Place your model file at `storage/ai/models/llama-3b.gguf` or update the path in `config/local-ai.php`.

   Download a GGUF model from Hugging Face, for example:
   https://huggingface.co

4. Run the install helper:

```bash
php artisan ai:install
```

## Usage

Use the facade from Laravel:

```php
use LocalAI;

$result = LocalAI::generate('Hello, world!', 50);
echo $result;
```

## Config

The package uses `config/local-ai.php` with this value:

```php
return [
    'model_path' => 'ai/models/llama-3b.gguf',
];
```

## Testing

To test, use a Laravel app and install the package as a local path repository. Then run:

```bash
php artisan ai:install
php artisan tinker
LocalAI::generate('Hello', 50)
```

## Notes

- Keep the `bin/` folders if you want to include the local binaries.
- The package depends on `symfony/process` and `illuminate/support`.
