<?php

namespace Emeq\McpLaravel\Application\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class MakeMcpResourceCommand extends Command
{
    protected $signature = 'make:mcp-resource {name : The name of the MCP resource}';

    protected $description = 'Create a new MCP resource class';

    public function handle(): int
    {
        $name = $this->argument('name');
        $className = Str::studly($name).'Resource';
        $namespace = $this->getDefaultNamespace();
        $path = $this->getPath($className);

        if (file_exists($path)) {
            $this->error("Resource {$className} already exists!");

            return self::FAILURE;
        }

        $this->makeDirectory($path);
        $this->filesystem()->put($path, $this->buildClass($className, $namespace, $name));

        $this->info("MCP Resource {$className} created successfully.");

        return self::SUCCESS;
    }

    protected function buildClass(string $name, string $namespace, string $resourceName): string
    {
        $stub = file_get_contents(__DIR__.'/stubs/mcp-resource.stub');

        return str_replace(
            ['{{namespace}}', '{{class}}', '{{name}}', '{{uri}}'],
            [$namespace, $name, Str::title(str_replace('-', ' ', Str::kebab($resourceName))), 'laravel://'.Str::kebab($resourceName)],
            $stub
        );
    }

    protected function getPath(string $name): string
    {
        return app_path('Mcp/Resources/'.$name.'.php');
    }

    protected function getDefaultNamespace(): string
    {
        return 'App\\Mcp\\Resources';
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

