<?php

namespace Emeq\McpLaravel\Domain\Mcp\Entities;

use Emeq\McpLaravel\Domain\Mcp\ValueObjects\PromptTemplate;

final class McpPrompt
{
    /**
     * @param  array<string, mixed>  $arguments
     */
    public function __construct(
        private readonly string $name,
        private readonly string $description,
        private readonly array $arguments,
        private readonly ?PromptTemplate $template = null,
        private readonly ?string $handler = null
    ) {}

    /**
     * Get the prompt name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the prompt description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the prompt arguments.
     *
     * @return array<string, mixed>
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Get the prompt template.
     */
    public function getTemplate(): ?PromptTemplate
    {
        return $this->template;
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
            'arguments' => $this->arguments,
        ];
    }
}
