<?php

namespace Emeq\McpLaravel\Application\Services;

use Emeq\McpLaravel\Domain\Boost\Contracts\BoostIntegrationInterface;
use Emeq\McpLaravel\Infrastructure\Boost\BoostContextProvider;
use Emeq\McpLaravel\Infrastructure\Boost\BoostGuidelineManager;

final class BoostIntegrationService
{
    public function __construct(
        private readonly BoostIntegrationInterface $boostIntegration,
        private readonly BoostGuidelineManager $guidelineManager,
        private readonly BoostContextProvider $contextProvider
    ) {}

    /**
     * Initialize Boost integration.
     */
    public function initialize(): void
    {
        if (! $this->boostIntegration->isEnabled()) {
            return;
        }

        $this->boostIntegration->initialize();
        $this->guidelineManager->loadGuidelines();
        $this->contextProvider->addApplicationContext();
    }

    /**
     * Get Boost guidelines.
     *
     * @return array<string, mixed>
     */
    public function getGuidelines(): array
    {
        return $this->guidelineManager->getAllGuidelines();
    }

    /**
     * Get guidelines for a specific context.
     *
     * @return array<string, mixed>
     */
    public function getGuidelinesForContext(string $context): array
    {
        return $this->guidelineManager->getGuidelinesForContext($context);
    }

    /**
     * Add context to Boost.
     *
     * @param  array<string, mixed>  $context
     */
    public function addContext(array $context): void
    {
        $this->contextProvider->addContext($context);
    }
}
