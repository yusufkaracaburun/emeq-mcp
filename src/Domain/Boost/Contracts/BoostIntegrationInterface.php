<?php

namespace Emeq\McpLaravel\Domain\Boost\Contracts;

interface BoostIntegrationInterface
{
    /**
     * Initialize the Boost integration.
     */
    public function initialize(): void;

    /**
     * Check if Boost is enabled.
     */
    public function isEnabled(): bool;

    /**
     * Get Boost guidelines.
     *
     * @return array<string, mixed>
     */
    public function getGuidelines(): array;

    /**
     * Provide context to Boost.
     *
     * @param  array<string, mixed>  $context
     */
    public function provideContext(array $context): void;
}
