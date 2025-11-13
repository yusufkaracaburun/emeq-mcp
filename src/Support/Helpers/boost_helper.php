<?php

use Emeq\McpLaravel\Application\Services\BoostIntegrationService;

if (! function_exists('boost_guidelines')) {
    /**
     * Get Boost guidelines.
     *
     * @return array<string, mixed>
     */
    function boost_guidelines(): array
    {
        return app(BoostIntegrationService::class)->getGuidelines();
    }
}

if (! function_exists('boost_guidelines_for')) {
    /**
     * Get Boost guidelines for a specific context.
     *
     * @return array<string, mixed>
     */
    function boost_guidelines_for(string $context): array
    {
        return app(BoostIntegrationService::class)->getGuidelinesForContext($context);
    }
}

if (! function_exists('boost_add_context')) {
    /**
     * Add context to Boost.
     *
     * @param  array<string, mixed>  $context
     */
    function boost_add_context(array $context): void
    {
        app(BoostIntegrationService::class)->addContext($context);
    }
}
