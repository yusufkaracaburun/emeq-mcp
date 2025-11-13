<?php

namespace Emeq\McpLaravel\Application\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class MakeMcpPromptCommand extends Command
{
    protected $signature = 'make:mcp-prompt {name : The name of the MCP prompt}';

    protected $description = 'Create a new MCP prompt class';

    public function handle(): int
    {
        $name = $this->argument('name');
        $className = Str::studly($name).'Prompt';
        $namespace = $this->getDefaultNamespace();
        $path = $this->getPath($className);

        if (file_exists($path)) {
            $this->error("Prompt {$className} already exists!");

            return self::FAILURE;
        }

        $this->makeDirectory($path);
        $this->filesystem()->put($path, $this->buildClass($className, $namespace, $name));

        $this->info("MCP Prompt {$className} created successfully.");

        return self::SUCCESS;
    }

    protected function buildClass(string $name, string $namespace, string $promptName): string
    {
        $stub = file_get_contents(__DIR__.'/stubs/mcp-prompt.stub');

        return str_replace(
            ['{{namespace}}', '{{class}}', '{{name}}'],
            [$namespace, $name, Str::kebab($promptName)],
            $stub
        );
    }

    protected function getPath(string $name): string
    {
        return app_path('Mcp/Prompts/'.$name.'.php');
    }

    protected function getDefaultNamespace(): string
    {
        return 'App\\Mcp\\Prompts';
    }

    protected function makeDirectory(string $path): void
    {
        if (! $this->filesystem()->isDirectory(dirname($path))) {
            $this->filesystem()->makeDirectory(dirname($path), 0755, true);
        }
    }

    protected function filesystem()
    {
        return app('files');
    }
}

