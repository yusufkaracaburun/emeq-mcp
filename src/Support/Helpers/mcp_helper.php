<?php

use Emeq\McpLaravel\Infrastructure\Mcp\Builders\PromptBuilder;
use Emeq\McpLaravel\Infrastructure\Mcp\Builders\ResourceBuilder;
use Emeq\McpLaravel\Infrastructure\Mcp\Builders\ServerBuilder;
use Emeq\McpLaravel\Infrastructure\Mcp\Builders\ToolBuilder;

if (! function_exists('mcp_server')) {
    /**
     * Create a new MCP server builder instance.
     */
    function mcp_server(): ServerBuilder
    {
        return new ServerBuilder;
    }
}

if (! function_exists('mcp_tool')) {
    /**
     * Create a new MCP tool builder instance.
     */
    function mcp_tool(): ToolBuilder
    {
        return new ToolBuilder;
    }
}

if (! function_exists('mcp_resource')) {
    /**
     * Create a new MCP resource builder instance.
     */
    function mcp_resource(): ResourceBuilder
    {
        return new ResourceBuilder;
    }
}

if (! function_exists('mcp_prompt')) {
    /**
     * Create a new MCP prompt builder instance.
     */
    function mcp_prompt(): PromptBuilder
    {
        return new PromptBuilder;
    }
}

