<?php

namespace Emeq\McpLaravel\Application\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class MakeMcpServerCommand extends Command
{
    protected $signature = 'make:mcp-server {name : The name of the MCP server}';

    protected $description = 'Create a new MCP server class';

    public function handle(): int
    {
        $name = $this->argument('name');
        $className = Str::studly($name).'Server';
        $namespace = $this->getDefaultNamespace();
        $path = $this->getPath($className);

        if (file_exists($path)) {
            $this->error("Server {$className} already exists!");

            return self::FAILURE;
        }

        $this->makeDirectory($path);
        $this->filesystem()->put($path, $this->buildClass($className, $namespace));

        $this->info("MCP Server {$className} created successfully.");

        return self::SUCCESS;
    }

    protected function buildClass(string $name, string $namespace): string
    {
        $stub = file_get_contents(__DIR__.'/stubs/mcp-server.stub');

        return str_replace(
            ['{{namespace}}', '{{class}}', '{{name}}'],
            [$namespace, $name, Str::title(str_replace('-', ' ', Str::kebab($name)))],
            $stub
        );
    }

    protected function getPath(string $name): string
    {
        return app_path('Mcp/Servers/'.$name.'.php');
    }

    protected function getDefaultNamespace(): string
    {
        return 'App\\Mcp\\Servers';
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
