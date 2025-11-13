<?php

namespace Emeq\McpLaravel\Domain\Boost\Services;

use Emeq\McpLaravel\Domain\Boost\Contracts\GuidelineProviderInterface;

final class BoostGuidelineService implements GuidelineProviderInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $guidelines = [];

    /**
     * @var array<string, array<string, mixed>>
     */
    private array $contextGuidelines = [];

    /**
     * Get all guidelines.
     *
     * @return array<string, mixed>
     */
    public function getGuidelines(): array
    {
        return $this->guidelines;
    }

    /**
     * Get guidelines for a specific context.
     *
     * @return array<string, mixed>
     */
    public function getGuidelinesForContext(string $context): array
    {
        return $this->contextGuidelines[$context] ?? [];
    }

    /**
     * Add a guideline.
     *
     * @param  array<string, mixed>  $guideline
     */
    public function addGuideline(array $guideline): void
    {
        $this->guidelines[] = $guideline;

        if (isset($guideline['context'])) {
            $context = $guideline['context'];
            if (! isset($this->contextGuidelines[$context])) {
                $this->contextGuidelines[$context] = [];
            }
            $this->contextGuidelines[$context][] = $guideline;
        }
    }

    /**
     * Load guidelines from a file or directory.
     */
    public function loadGuidelines(string $path): void
    {
        if (is_file($path)) {
            $this->loadGuidelinesFromFile($path);
        } elseif (is_dir($path)) {
            $this->loadGuidelinesFromDirectory($path);
        }
    }

    /**
     * Load guidelines from a single file.
     */
    private function loadGuidelinesFromFile(string $filePath): void
    {
        if (! file_exists($filePath)) {
            return;
        }

        $content = file_get_contents($filePath);
        $guidelines = json_decode($content, true);

        if (is_array($guidelines)) {
            foreach ($guidelines as $guideline) {
                $this->addGuideline($guideline);
            }
        }
    }

    /**
     * Load guidelines from a directory.
     */
    private function loadGuidelinesFromDirectory(string $directory): void
    {
        $files = glob($directory.'/*.json');

        foreach ($files as $file) {
            $this->loadGuidelinesFromFile($file);
        }
    }
}

