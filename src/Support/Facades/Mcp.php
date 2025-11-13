<?php

namespace Emeq\McpLaravel\Support\Facades;

use Emeq\McpLaravel\Application\Services\McpRegistryService;
use Emeq\McpLaravel\Infrastructure\Mcp\Builders\PromptBuilder;
use Emeq\McpLaravel\Infrastructure\Mcp\Builders\ResourceBuilder;
use Emeq\McpLaravel\Infrastructure\Mcp\Builders\ServerBuilder;
use Emeq\McpLaravel\Infrastructure\Mcp\Builders\ToolBuilder;
use Illuminate\Support\Facades\Facade;

/**
 * @method static ServerBuilder server()
 * @method static ToolBuilder tool()
 * @method static ResourceBuilder resource()
 * @method static PromptBuilder prompt()
 * @method static McpRegistryService registry()
 *
 * @see \Emeq\McpLaravel\Application\Services\McpRegistryService
 */
class Mcp extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return McpRegistryService::class;
    }

    /**
     * Create a new server builder instance.
     */
    public static function server(): ServerBuilder
    {
        return new ServerBuilder;
    }

    /**
     * Create a new tool builder instance.
     */
    public static function tool(): ToolBuilder
    {
        return new ToolBuilder;
    }

    /**
     * Create a new resource builder instance.
     */
    public static function resource(): ResourceBuilder
    {
        return new ResourceBuilder;
    }

    /**
     * Create a new prompt builder instance.
     */
    public static function prompt(): PromptBuilder
    {
        return new PromptBuilder;
    }

    /**
     * Get the registry service instance.
     */
    public static function registry(): McpRegistryService
    {
        return app(McpRegistryService::class);
    }
}

