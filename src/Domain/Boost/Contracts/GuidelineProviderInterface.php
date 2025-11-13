<?php

namespace Emeq\McpLaravel\Domain\Boost\Contracts;

interface GuidelineProviderInterface
{
    /**
     * Get all guidelines.
     *
     * @return array<string, mixed>
     */
    public function getGuidelines(): array;

    /**
     * Get guidelines for a specific context.
     *
     * @return array<string, mixed>
     */
    public function getGuidelinesForContext(string $context): array;

    /**
     * Add a guideline.
     *
     * @param  array<string, mixed>  $guideline
     */
    public function addGuideline(array $guideline): void;

    /**
     * Load guidelines from a file or directory.
     */
    public function loadGuidelines(string $path): void;
}

