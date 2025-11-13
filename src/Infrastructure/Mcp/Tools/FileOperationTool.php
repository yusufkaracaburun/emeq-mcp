<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp\Tools;

use Emeq\McpLaravel\Infrastructure\Mcp\BaseTool;
use Exception;
use Illuminate\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\File;
use InvalidArgumentException;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use RuntimeException;

final class FileOperationTool extends BaseTool
{
    public function getName(): string
    {
        return 'file-operation';
    }

    public function getDescription(): string
    {
        return 'Perform file system operations (read, write, delete, list). Only allowed paths can be accessed.';
    }

    public function getInputSchema(JsonSchema $schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'operation' => [
                    'type' => 'string',
                    'description' => 'The operation to perform: read, write, delete, list',
                    'enum' => ['read', 'write', 'delete', 'list'],
                ],
                'path' => [
                    'type' => 'string',
                    'description' => 'File or directory path',
                ],
                'content' => [
                    'type' => 'string',
                    'description' => 'File content (required for write)',
                ],
            ],
            'required' => ['operation', 'path'],
        ];
    }

    public function handle(Request $request): Response
    {
        if (! config('emeq-mcp.tools.file_operation.enabled', true)) {
            return Response::error('File operation tool is disabled.');
        }

        $arguments = $this->validateArguments($request->arguments());
        $operation = $arguments['operation'];
        $path = $arguments['path'];

        // Check if path is allowed
        if (! $this->isPathAllowed($path)) {
            return Response::error("Path '{$path}' is not in the allowed list.");
        }

        try {
            $result = match ($operation) {
                'read' => $this->readFile($path),
                'write' => $this->writeFile($path, $arguments['content'] ?? null),
                'delete' => $this->deleteFile($path),
                'list' => $this->listDirectory($path),
                default => throw new InvalidArgumentException("Unknown operation: {$operation}"),
            };

            return Response::text(json_encode($result, JSON_PRETTY_PRINT));
        } catch (Exception $e) {
            return Response::error("File operation failed: {$e->getMessage()}");
        }
    }

    /**
     * Check if a path is allowed.
     */
    private function isPathAllowed(string $path): bool
    {
        $allowedPaths = config('emeq-mcp.tools.file_operation.allowed_paths', []);

        if (empty($allowedPaths)) {
            // If no restrictions, allow all paths within project root
            return str_starts_with(realpath($path) ?: $path, realpath(base_path()) ?: base_path());
        }

        foreach ($allowedPaths as $allowedPath) {
            if (str_starts_with($path, $allowedPath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Read a file.
     *
     * @return array<string, mixed>
     */
    private function readFile(string $path): array
    {
        if (! File::exists($path)) {
            throw new RuntimeException("File not found: {$path}");
        }

        return [
            'operation' => 'read',
            'path' => $path,
            'content' => File::get($path),
            'size' => File::size($path),
        ];
    }

    /**
     * Write a file.
     *
     * @return array<string, mixed>
     */
    private function writeFile(string $path, ?string $content): array
    {
        if ($content === null) {
            throw new InvalidArgumentException('Content is required for write operation.');
        }

        File::put($path, $content);

        return [
            'operation' => 'write',
            'path' => $path,
            'success' => true,
        ];
    }

    /**
     * Delete a file.
     *
     * @return array<string, mixed>
     */
    private function deleteFile(string $path): array
    {
        if (! File::exists($path)) {
            throw new RuntimeException("File not found: {$path}");
        }

        File::delete($path);

        return [
            'operation' => 'delete',
            'path' => $path,
            'success' => true,
        ];
    }

    /**
     * List directory contents.
     *
     * @return array<string, mixed>
     */
    private function listDirectory(string $path): array
    {
        if (! File::isDirectory($path)) {
            throw new RuntimeException("Path is not a directory: {$path}");
        }

        $files = File::files($path);
        $directories = File::directories($path);

        return [
            'operation' => 'list',
            'path' => $path,
            'files' => array_map(fn ($file) => $file->getFilename(), $files),
            'directories' => array_map(fn ($dir) => basename($dir), $directories),
        ];
    }
}
