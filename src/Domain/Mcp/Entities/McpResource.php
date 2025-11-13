<?php

namespace Emeq\McpLaravel\Domain\Mcp\Entities;

use Emeq\McpLaravel\Domain\Mcp\ValueObjects\ResourceUri;

final class McpResource
{
    public function __construct(
        private readonly ResourceUri $uri,
        private readonly string $name,
        private readonly string $description,
        private readonly string $mimeType,
        private readonly ?string $handler = null
    ) {
    }

    /**
     * Get the resource URI.
     */
    public function getUri(): ResourceUri
    {
        return $this->uri;
    }

    /**
     * Get the resource name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the resource description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get the MIME type.
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
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
            'uri' => $this->uri->toString(),
            'name' => $this->name,
            'description' => $this->description,
            'mimeType' => $this->mimeType,
        ];
    }
}

