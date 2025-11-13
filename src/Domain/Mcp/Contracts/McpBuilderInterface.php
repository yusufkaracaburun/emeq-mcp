<?php

namespace Emeq\McpLaravel\Domain\Mcp\Contracts;

interface McpBuilderInterface
{
    /**
     * Build and return the configured component.
     *
     * @return mixed
     */
    public function build();
}

