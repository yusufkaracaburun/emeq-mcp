<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp;

use Emeq\McpLaravel\Domain\Mcp\Contracts\ResourceInterface;
use Emeq\McpLaravel\Domain\Mcp\ValueObjects\ResourceUri;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Resource;

abstract class BaseResource extends Resource implements ResourceInterface
{
    /**
     * Get the resource URI.
     */
    abstract public function getUri(): string;

    /**
     * Get the resource name.
     */
    abstract public function getName(): string;

    /**
     * Get the resource description.
     */
    abstract public function getDescription(): string;

    /**
     * Get the resource MIME type.
     */
    abstract public function getMimeType(): string;

    /**
     * Handle the resource request.
     */
    abstract public function handle(Request $request): Response;

    /**
     * Create a ResourceUri value object from the URI string.
     */
    protected function createResourceUri(string $uri): ResourceUri
    {
        return new ResourceUri($uri);
    }
}
