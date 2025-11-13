<?php

namespace Emeq\McpLaravel\Infrastructure\Boost;

use Emeq\McpLaravel\Domain\Boost\Contracts\BoostIntegrationInterface;

final class BoostContextProvider
{
    /**
     * @var array<string, mixed>
     */
    private array $context = [];

    public function __construct(
        private readonly BoostIntegrationInterface $boostIntegration
    ) {}

    /**
     * Add context data.
     *
     * @param  array<string, mixed>  $context
     */
    public function addContext(array $context): void
    {
        $this->context = array_merge($this->context, $context);
        $this->boostIntegration->provideContext($this->context);
    }

    /**
     * Get all context.
     *
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Clear context.
     */
    public function clearContext(): void
    {
        $this->context = [];
    }

    /**
     * Add application context (routes, models, etc.).
     */
    public function addApplicationContext(): void
    {
        $config = app('config');
        $this->addContext([
            'application' => [
                'name' => $config->get('app.name'),
                'env' => $config->get('app.env'),
                'version' => $config->get('app.version', '1.0.0'),
            ],
        ]);
    }
}
