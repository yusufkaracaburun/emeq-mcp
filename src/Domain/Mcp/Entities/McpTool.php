<?php

namespace Emeq\McpLaravel\Domain\Mcp\Entities;

use Emeq\McpLaravel\Domain\Mcp\ValueObjects\ToolSchema;

final class McpTool
{
    /**
     * @param  array<string, mixed>  $inputSchema
     */
    public function __construct(
        private readonly string $name,
        private readonly string $description,
        private readonly ToolSchema $inputSchema,
        private readonly ?string $handler = null
    ) {}

    /**
     * Get the tool name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the tool description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the input schema.
     */
    public function getInputSchema(): ToolSchema
    {
        return $this->inputSchema;
    }

    /**
     * Get the handler class.
     */
    public function getHandler(): ?string
    {
        return $this->handler;
    }

    /**
     * Convert to array representation.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'inputSchema' => $this->inputSchema->toArray(),
        ];
    }
}
