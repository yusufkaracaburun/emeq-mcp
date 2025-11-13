<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp;

use Laravel\Mcp\Server;

abstract class BaseServer extends Server
{
    /**
     * The MCP server's name.
     */
    protected string $name;

    /**
     * The MCP server's version.
     */
    protected string $version;

    /**
     * The MCP server's instructions for the LLM.
     */
    protected string $instructions;

    /**
     * The tools registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    protected array $tools = [];

    /**
     * The resources registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Resource>>
     */
    protected array $resources = [];

    /**
     * The prompts registered with this MCP server.
     *
     * @var array<int, class-string<\Laravel\Mcp\Server\Prompt>>
     */
    protected array $prompts = [];

    /**
     * Get the server name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the server version.
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Get the server instructions.
     */
    public function getInstructions(): string
    {
        return $this->instructions;
    }

    /**
     * Get the tools registered with this server.
     *
     * @return array<int, class-string<\Laravel\Mcp\Server\Tool>>
     */
    public function getTools(): array
    {
        return $this->tools;
    }

    /**
     * Get the resources registered with this server.
     *
     * @return array<int, class-string<\Laravel\Mcp\Server\Resource>>
     */
    public function getResources(): array
    {
        return $this->resources;
    }

    /**
     * Get the prompts registered with this server.
     *
     * @return array<int, class-string<\Laravel\Mcp\Server\Prompt>>
     */
    public function getPrompts(): array
    {
        return $this->prompts;
    }
}

