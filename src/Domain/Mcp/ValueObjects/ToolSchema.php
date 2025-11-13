<?php

namespace Emeq\McpLaravel\Domain\Mcp\ValueObjects;

use InvalidArgumentException;

final class ToolSchema
{
    /**
     * @param  array<string, mixed>  $schema
     */
    public function __construct(
        private readonly array $schema
    ) {
        $this->validate();
    }

    /**
     * Get the schema as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->schema;
    }

    /**
     * Get a property from the schema.
     */
    public function getProperty(string $key, mixed $default = null): mixed
    {
        return $this->schema[$key] ?? $default;
    }

    /**
     * Check if a property exists in the schema.
     */
    public function hasProperty(string $key): bool
    {
        return isset($this->schema[$key]);
    }

    /**
     * Validate the schema structure.
     *
     * @throws InvalidArgumentException
     */
    private function validate(): void
    {
        if (empty($this->schema)) {
            throw new InvalidArgumentException('Tool schema cannot be empty.');
        }

        if (! isset($this->schema['type']) && ! isset($this->schema['properties'])) {
            throw new InvalidArgumentException('Tool schema must have either "type" or "properties" key.');
        }
    }
}
