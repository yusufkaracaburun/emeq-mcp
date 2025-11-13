<?php

namespace Emeq\McpLaravel\Domain\Mcp\Contracts;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

/**
 * Interface for MCP prompts.
 */
interface PromptInterface
{
    /**
     * Get the prompt name.
     */
    public function getName(): string;

    /**
     * Get the prompt description.
     */
    public function getDescription(): string;

    /**
     * Get the prompt arguments schema.
     *
     * @return array<string, mixed>
     */
    public function getArguments(): array;

    /**
     * Handle the prompt request.
     */
    public function handle(Request $request): Response;
}

