<?php

namespace Emeq\McpLaravel\Application\Services;

use Emeq\McpLaravel\Infrastructure\Mcp\Prompts\CodeGenerationPrompt;
use Emeq\McpLaravel\Infrastructure\Mcp\Prompts\DatabaseDesignPrompt;
use Emeq\McpLaravel\Infrastructure\Mcp\Prompts\DebuggingPrompt;
use Emeq\McpLaravel\Infrastructure\Mcp\Resources\ConfigResource;
use Emeq\McpLaravel\Infrastructure\Mcp\Resources\LogResource;
use Emeq\McpLaravel\Infrastructure\Mcp\Resources\ModelSchemaResource;
use Emeq\McpLaravel\Infrastructure\Mcp\Resources\RouteListResource;
use Emeq\McpLaravel\Infrastructure\Mcp\Tools\ArtisanCommandTool;
use Emeq\McpLaravel\Infrastructure\Mcp\Tools\CacheOperationTool;
use Emeq\McpLaravel\Infrastructure\Mcp\Tools\DatabaseQueryTool;
use Emeq\McpLaravel\Infrastructure\Mcp\Tools\FileOperationTool;
use Emeq\McpLaravel\Infrastructure\Mcp\Tools\ModelOperationTool;
use Emeq\McpLaravel\Infrastructure\Mcp\Tools\QueueJobTool;
use Laravel\Mcp\Facades\Mcp;

final class McpRegistryService
{
    /**
     * Register all pre-built components.
     */
    public function registerPreBuiltComponents(): void
    {
        $this->registerPreBuiltTools();
        $this->registerPreBuiltResources();
        $this->registerPreBuiltPrompts();
    }

    /**
     * Register pre-built tools.
     */
    public function registerPreBuiltTools(): void
    {
        $tools = [
            DatabaseQueryTool::class => 'tools.database_query.enabled',
            ModelOperationTool::class => 'tools.model_operation.enabled',
            ArtisanCommandTool::class => 'tools.artisan_command.enabled',
            CacheOperationTool::class => 'tools.cache_operation.enabled',
            QueueJobTool::class => 'tools.queue_job.enabled',
            FileOperationTool::class => 'tools.file_operation.enabled',
        ];

        foreach ($tools as $configKey) {
            if (config("emeq-mcp.{$configKey}", true)) {
                // Tools are registered via server classes, not directly here
                // This is a placeholder for future direct registration if needed
            }
        }
    }

    /**
     * Register pre-built resources.
     */
    public function registerPreBuiltResources(): void
    {
        $resources = [
            ModelSchemaResource::class => 'resources.model_schema.enabled',
            RouteListResource::class => 'resources.route_list.enabled',
            ConfigResource::class => 'resources.config.enabled',
            LogResource::class => 'resources.log.enabled',
        ];

        foreach ($resources as $configKey) {
            if (config("emeq-mcp.{$configKey}", true)) {
                // Resources are registered via server classes, not directly here
                // This is a placeholder for future direct registration if needed
            }
        }
    }

    /**
     * Register pre-built prompts.
     */
    public function registerPreBuiltPrompts(): void
    {
        $prompts = [
            CodeGenerationPrompt::class => 'prompts.code_generation.enabled',
            DebuggingPrompt::class => 'prompts.debugging.enabled',
            DatabaseDesignPrompt::class => 'prompts.database_design.enabled',
        ];

        foreach ($prompts as $configKey) {
            if (config("emeq-mcp.{$configKey}", true)) {
                // Prompts are registered via server classes, not directly here
                // This is a placeholder for future direct registration if needed
            }
        }
    }

    /**
     * Get list of registered components.
     *
     * @return array<string, mixed>
     */
    public function getRegisteredComponents(): array
    {
        return [
            'tools' => $this->getRegisteredTools(),
            'resources' => $this->getRegisteredResources(),
            'prompts' => $this->getRegisteredPrompts(),
        ];
    }

    /**
     * Get registered tools.
     *
     * @return array<int, string>
     */
    private function getRegisteredTools(): array
    {
        return [
            DatabaseQueryTool::class,
            ModelOperationTool::class,
            ArtisanCommandTool::class,
            CacheOperationTool::class,
            QueueJobTool::class,
            FileOperationTool::class,
        ];
    }

    /**
     * Get registered resources.
     *
     * @return array<int, string>
     */
    private function getRegisteredResources(): array
    {
        return [
            ModelSchemaResource::class,
            RouteListResource::class,
            ConfigResource::class,
            LogResource::class,
        ];
    }

    /**
     * Get registered prompts.
     *
     * @return array<int, string>
     */
    private function getRegisteredPrompts(): array
    {
        return [
            CodeGenerationPrompt::class,
            DebuggingPrompt::class,
            DatabaseDesignPrompt::class,
        ];
    }
}

