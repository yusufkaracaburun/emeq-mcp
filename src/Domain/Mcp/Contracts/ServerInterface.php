<?php

namespace Emeq\McpLaravel\Domain\Mcp\Contracts;

use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

interface ServerInterface
{
    /**
     * Get the server name.
     */
    public function getName(): string;

    /**
     * Get the server version.
     */
    public function getVersion(): string;

    /**
     * Get the server instructions.
     */
    public function getInstructions(): string;

    /**
     * Register tools with the server.
     *
     * @param  array<int, class-string>  $tools
     */
    public function registerTools(array $tools): void;

    /**
     * Register resources with the server.
     *
     * @param  array<int, class-string>  $resources
     */
    public function registerResources(array $resources): void;

    /**
     * Register prompts with the server.
     *
     * @param  array<int, class-string>  $prompts
     */
    public function registerPrompts(array $prompts): void;
}

