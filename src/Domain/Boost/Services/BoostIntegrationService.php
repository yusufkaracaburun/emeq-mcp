<?php

namespace Emeq\McpLaravel\Domain\Boost\Services;

use Emeq\McpLaravel\Domain\Boost\Contracts\BoostIntegrationInterface;
use Emeq\McpLaravel\Domain\Boost\Contracts\GuidelineProviderInterface;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

final class BoostIntegrationService implements BoostIntegrationInterface
{
    public function __construct(
        private readonly GuidelineProviderInterface $guidelineProvider,
        private readonly ConfigRepository $config
    ) {
    }

    /**
     * Initialize the Boost integration.
     */
    public function initialize(): void
    {
        if (! $this->isEnabled()) {
            return;
        }

        $guidelinesPath = $this->config->get('emeq-mcp.boost.guidelines_path');

        if ($guidelinesPath && is_dir($guidelinesPath)) {
            $this->guidelineProvider->loadGuidelines($guidelinesPath);
        }
    }

    /**
     * Check if Boost is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->config->get('emeq-mcp.boost.enabled', false);
    }

    /**
     * Get Boost guidelines.
     *
     * @return array<string, mixed>
     */
    public function getGuidelines(): array
    {
        return $this->guidelineProvider->getGuidelines();
    }

    /**
     * Provide context to Boost.
     *
     * @param  array<string, mixed>  $context
     */
    public function provideContext(array $context): void
    {
        // This can be extended to integrate with Boost's context system
        // For now, we'll store it for potential future use
    }
}

