<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp\Builders;

use Emeq\McpLaravel\Domain\Mcp\Contracts\McpBuilderInterface;
use Emeq\McpLaravel\Domain\Mcp\ValueObjects\ResourceUri;
use Emeq\McpLaravel\Infrastructure\Mcp\BaseResource;

final class ResourceBuilder implements McpBuilderInterface
{
    private string $uri;
    private string $name;
    private string $description;
    private string $mimeType = 'text/plain';
    private ?string $handler = null;

    /**
     * Set the resource URI.
     */
    public function uri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Set the resource name.
     */
    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the resource description.
     */
    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set the MIME type.
     */
    public function mimeType(string $mimeType): self
    {
        $this->mimeType = $mimeType;

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
     * Build and return a resource instance.
     */
    public function build(): BaseResource
    {
        $resourceUri = new ResourceUri($this->uri);

        return new class($resourceUri, $this->name, $this->description, $this->mimeType, $this->handler) extends BaseResource
        {
            public function __construct(
                private readonly ResourceUri $uri,
                private readonly string $name,
                private readonly string $description,
                private readonly string $mimeType,
                private readonly ?string $handler
            ) {
            }

            public function getUri(): string
            {
                return $this->uri->toString();
            }

            public function getName(): string
            {
                return $this->name;
            }

            public function getDescription(): string
            {
                return $this->description;
            }

            public function getMimeType(): string
            {
                return $this->mimeType;
            }

            public function handle(\Laravel\Mcp\Request $request): \Laravel\Mcp\Response
            {
                if ($this->handler) {
                    $handler = app($this->handler);

                    return $handler->handle($request);
                }

                return \Laravel\Mcp\Response::text('Resource accessed successfully.');
            }
        };
    }
}

