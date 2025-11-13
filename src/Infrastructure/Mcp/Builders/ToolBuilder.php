<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp\Builders;

use Emeq\McpLaravel\Domain\Mcp\Contracts\McpBuilderInterface;
use Emeq\McpLaravel\Domain\Mcp\ValueObjects\ToolSchema;
use Emeq\McpLaravel\Infrastructure\Mcp\BaseTool;

final class ToolBuilder implements McpBuilderInterface
{
    private string $name;

    private string $description;

    private array $inputSchema = [];

    private ?string $handler = null;

    /**
     * Set the tool name.
     */
    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the tool description.
     */
    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set the input schema.
     *
     * @param  array<string, mixed>  $schema
     */
    public function inputSchema(array $schema): self
    {
        $this->inputSchema = $schema;

        return $this;
    }

    /**
     * Set the handler class.
     */
    public function handler(string $handler): self
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * Build and return a tool instance.
     */
    public function build(): BaseTool
    {
        $schema = new ToolSchema($this->inputSchema);
        $name = $this->name;
        $description = $this->description;
        $handler = $this->handler;

        return new class($name, $description, $schema, $handler) extends BaseTool
        {
            public function __construct(
                private string $toolName,
                private string $toolDescription,
                private ToolSchema $schema,
                private ?string $handler
            ) {}

            public function getName(): string
            {
                return $this->toolName;
            }

            public function getDescription(): string
            {
                return $this->toolDescription;
            }

            public function getInputSchema(): array
            {
                return $this->schema->toArray();
            }

            public function handle(\Laravel\Mcp\Request $request): \Laravel\Mcp\Response
            {
                if ($this->handler) {
                    $handler = app($this->handler);

                    return $handler->handle($request);
                }

                return \Laravel\Mcp\Response::text('Tool executed successfully.');
            }
        };
    }
}
