<?php

namespace Emeq\McpLaravel\Domain\Mcp\Contracts;

use Illuminate\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

interface ToolInterface
{
    /**
     * Get the tool name.
     */
    public function getName(): string;

    /**
     * Get the tool description.
     */
    public function getDescription(): string;

    /**
     * Get the tool input schema.
     *
     * @return array<string, mixed>
     */
    public function getInputSchema(JsonSchema $schema): array;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response;
}
