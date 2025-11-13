<?php

namespace Emeq\McpLaravel\Domain\Mcp\ValueObjects;

use InvalidArgumentException;

final class ResourceUri
{
    public function __construct(
        private readonly string $uri
    ) {
        $this->validate();
    }

    /**
     * Get the URI as a string.
     */
    public function toString(): string
    {
        return $this->uri;
    }

    /**
     * Get the URI scheme.
     */
    public function getScheme(): ?string
    {
        $parts = parse_url($this->uri);

        return $parts['scheme'] ?? null;
    }

    /**
     * Get the URI path.
     */
    public function getPath(): string
    {
        $parts = parse_url($this->uri);

        return $parts['path'] ?? $this->uri;
    }

    /**
     * Check if the URI is valid.
     */
    public function isValid(): bool
    {
        return filter_var($this->uri, FILTER_VALIDATE_URL) !== false
            || $this->isValidCustomUri();
    }

    /**
     * Check if it's a valid custom URI (e.g., laravel://model/User).
     */
    private function isValidCustomUri(): bool
    {
        return preg_match('/^[a-z][a-z0-9+.-]*:/i', $this->uri) === 1;
    }

    /**
     * Validate the URI.
     *
     * @throws InvalidArgumentException
     */
    private function validate(): void
    {
        if (empty($this->uri)) {
            throw new InvalidArgumentException('Resource URI cannot be empty.');
        }

        if (! $this->isValid()) {
            throw new InvalidArgumentException("Invalid resource URI: {$this->uri}");
        }
    }
}
