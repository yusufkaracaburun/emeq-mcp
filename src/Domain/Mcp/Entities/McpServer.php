<?php

namespace Emeq\McpLaravel\Domain\Mcp\Entities;

final class McpServer
{
    /**
     * @param  array<int, class-string>  $tools
     * @param  array<int, class-string>  $resources
     * @param  array<int, class-string>  $prompts
     */
    public function __construct(
        private readonly string $name,
        private readonly string $version,
        private readonly string $instructions,
        private array $tools = [],
        private array $resources = [],
        private array $prompts = []
    ) {}

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
     * Get registered tools.
     *
     * @return array<int, class-string>
     */
    public function getTools(): array
    {
        return $this->tools;
    }

    /**
     * Get registered resources.
     *
     * @return array<int, class-string>
     */
    public function getResources(): array
    {
        return $this->resources;
    }

    /**
     * Get registered prompts.
     *
     * @return array<int, class-string>
     */
    public function getPrompts(): array
    {
        return $this->prompts;
    }

    /**
     * Register a tool.
     *
     * @param  class-string  $tool
     */
    public function registerTool(string $tool): void
    {
        if (! in_array($tool, $this->tools, true)) {
            $this->tools[] = $tool;
        }
    }

    /**
     * Register a resource.
     *
     * @param  class-string  $resource
     */
    public function registerResource(string $resource): void
    {
        if (! in_array($resource, $this->resources, true)) {
            $this->resources[] = $resource;
        }
    }

    /**
     * Register a prompt.
     *
     * @param  class-string  $prompt
     */
    public function registerPrompt(string $prompt): void
    {
        if (! in_array($prompt, $this->prompts, true)) {
            $this->prompts[] = $prompt;
        }
    }

    /**
     * Check if the server has any components registered.
     */
    public function hasComponents(): bool
    {
        return ! empty($this->tools) || ! empty($this->resources) || ! empty($this->prompts);
    }
}
