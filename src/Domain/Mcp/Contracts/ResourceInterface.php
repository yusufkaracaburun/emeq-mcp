<?php

namespace Emeq\McpLaravel\Domain\Mcp\Contracts;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

interface ResourceInterface
{
    /**
     * Get the resource URI.
     */
    public function getUri(): string;

    /**
     * Get the resource name.
     */
    public function getName(): string;

    /**
     * Get the resource description.
     */
    public function getDescription(): string;

    /**
     * Get the resource MIME type.
     */
    public function getMimeType(): string;

    /**
     * Handle the resource request.
     */
    public function handle(Request $request): Response;
}

