<?php

namespace Emeq\McpLaravel\Application\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class MakeMcpToolCommand extends Command
{
    protected $signature = 'make:mcp-tool {name : The name of the MCP tool}';

    protected $description = 'Create a new MCP tool class';

    public function handle(): int
    {
        $name = $this->argument('name');
        $className = Str::studly($name).'Tool';
        $namespace = $this->getDefaultNamespace();
        $path = $this->getPath($className);

        if (file_exists($path)) {
            $this->error("Tool {$className} already exists!");

            return self::FAILURE;
        }

        $this->makeDirectory($path);
        $this->filesystem()->put($path, $this->buildClass($className, $namespace, $name));

        $this->info("MCP Tool {$className} created successfully.");

        return self::SUCCESS;
    }

    protected function buildClass(string $name, string $namespace, string $toolName): string
    {
        $stub = file_get_contents(__DIR__.'/stubs/mcp-tool.stub');

        return str_replace(
            ['{{namespace}}', '{{class}}', '{{name}}'],
            [$namespace, $name, Str::kebab($toolName)],
            $stub
        );
    }

    protected function getPath(string $name): string
    {
        return app_path('Mcp/Tools/'.$name.'.php');
    }

    protected function getDefaultNamespace(): string
    {
        return 'App\\Mcp\\Tools';
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
