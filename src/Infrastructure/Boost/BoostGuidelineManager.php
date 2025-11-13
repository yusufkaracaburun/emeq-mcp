<?php

namespace Emeq\McpLaravel\Infrastructure\Boost;

use Emeq\McpLaravel\Domain\Boost\Services\BoostGuidelineService;

final class BoostGuidelineManager
{
    public function __construct(
        private readonly BoostGuidelineService $guidelineService
    ) {}

    /**
     * Load guidelines from configured path.
     */
    public function loadGuidelines(): void
    {
        $guidelinesPath = app('config')->get('emeq-mcp.boost.guidelines_path');

        if ($guidelinesPath && is_dir($guidelinesPath)) {
            $this->guidelineService->loadGuidelines($guidelinesPath);
        }
    }

    /**
     * Get all guidelines.
     *
     * @return array<string, mixed>
     */
    public function getAllGuidelines(): array
    {
        return $this->guidelineService->getGuidelines();
    }

    /**
     * Get guidelines for a specific context.
     *
     * @return array<string, mixed>
     */
    public function getGuidelinesForContext(string $context): array
    {
        return $this->guidelineService->getGuidelinesForContext($context);
    }

    /**
     * Add a guideline.
     *
     * @param  array<string, mixed>  $guideline
     */
    public function addGuideline(array $guideline): void
    {
        $this->guidelineService->addGuideline($guideline);
    }
}
