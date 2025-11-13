<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp\Builders;

use Emeq\McpLaravel\Domain\Mcp\Contracts\McpBuilderInterface;
use Emeq\McpLaravel\Infrastructure\Mcp\BaseServer;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

final class ServerBuilder implements McpBuilderInterface
{
    private string $name;

    private string $version;

    private string $instructions;

    private array $tools = [];

    private array $resources = [];

    private array $prompts = [];

    public function __construct(?ConfigRepository $config = null)
    {
        $config = $config ?? app('config');
        $this->name = $config->get('emeq-mcp.server.default_name', 'Laravel MCP Server');
        $this->version = $config->get('emeq-mcp.server.default_version', '1.0.0');
        $this->instructions = '';
    }

    /**
     * Set the server name.
     */
    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the server version.
     */
    public function version(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Set the server instructions.
     */
    public function instructions(string $instructions): self
    {
        $this->instructions = $instructions;

        return $this;
    }

    /**
     * Add a tool to the server.
     *
     * @param  class-string  $tool
     */
    public function withTool(string $tool): self
    {
        $this->tools[] = $tool;

        return $this;
    }

    /**
     * Add multiple tools to the server.
     *
     * @param  array<int, class-string>  $tools
     */
    public function withTools(array $tools): self
    {
        $this->tools = array_merge($this->tools, $tools);

        return $this;
    }

    /**
     * Add a resource to the server.
     *
     * @param  class-string  $resource
     */
    public function withResource(string $resource): self
    {
        $this->resources[] = $resource;

        return $this;
    }

    /**
     * Add multiple resources to the server.
     *
     * @param  array<int, class-string>  $resources
     */
    public function withResources(array $resources): self
    {
        $this->resources = array_merge($this->resources, $resources);

        return $this;
    }

    /**
     * Add a prompt to the server.
     *
     * @param  class-string  $prompt
     */
    public function withPrompt(string $prompt): self
    {
        $this->prompts[] = $prompt;

        return $this;
    }

    /**
     * Add multiple prompts to the server.
     *
     * @param  array<int, class-string>  $prompts
     */
    public function withPrompts(array $prompts): self
    {
        $this->prompts = array_merge($this->prompts, $prompts);

        return $this;
    }

    /**
     * Build and return a server instance.
     */
    public function build(): BaseServer
    {
        return new class($this->name, $this->version, $this->instructions, $this->tools, $this->resources, $this->prompts) extends BaseServer
        {
            public function __construct(
                string $name,
                string $version,
                string $instructions,
                array $tools,
                array $resources,
                array $prompts
            ) {
                $this->name = $name;
                $this->version = $version;
                $this->instructions = $instructions;
                $this->tools = $tools;
                $this->resources = $resources;
                $this->prompts = $prompts;
            }
        };
    }
}
