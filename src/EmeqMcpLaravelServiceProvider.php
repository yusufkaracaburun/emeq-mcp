<?php

namespace Emeq\McpLaravel;

use Emeq\McpLaravel\Application\Commands\BoostInstallCommand;
use Emeq\McpLaravel\Application\Commands\MakeMcpPromptCommand;
use Emeq\McpLaravel\Application\Commands\MakeMcpResourceCommand;
use Emeq\McpLaravel\Application\Commands\MakeMcpServerCommand;
use Emeq\McpLaravel\Application\Commands\MakeMcpToolCommand;
use Emeq\McpLaravel\Application\Commands\McpListCommand;
use Emeq\McpLaravel\Application\Services\BoostIntegrationService;
use Emeq\McpLaravel\Application\Services\McpRegistryService;
use Emeq\McpLaravel\Domain\Boost\Contracts\BoostIntegrationInterface;
use Emeq\McpLaravel\Domain\Boost\Contracts\GuidelineProviderInterface;
use Emeq\McpLaravel\Domain\Boost\Services\BoostGuidelineService;
use Emeq\McpLaravel\Domain\Boost\Services\BoostIntegrationService as DomainBoostIntegrationService;
use Emeq\McpLaravel\Infrastructure\Boost\BoostContextProvider;
use Emeq\McpLaravel\Infrastructure\Boost\BoostGuidelineManager;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class EmeqMcpLaravelServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('emeq-mcp-laravel')
            ->hasConfigFile('emeq-mcp')
            ->hasCommands([
                MakeMcpServerCommand::class,
                MakeMcpToolCommand::class,
                MakeMcpResourceCommand::class,
                MakeMcpPromptCommand::class,
                BoostInstallCommand::class,
                McpListCommand::class,
            ]);
    }

    public function packageBooted(): void
    {
        // Publish routes file
        $this->publishes([
            __DIR__.'/../../routes/ai.php.stub' => base_path('routes/ai.php'),
        ], 'emeq-mcp-routes');

        // Register MCP routes if routes file exists
        if (file_exists($routesPath = base_path('routes/ai.php'))) {
            $this->loadRoutesFrom($routesPath);
        }

        // Auto-register pre-built tools/resources/prompts if enabled
        if ($this->app['config']->get('emeq-mcp.auto_register', true)) {
            $this->app->make(McpRegistryService::class)->registerPreBuiltComponents();
        }
    }

    public function packageRegistered(): void
    {
        // Register domain services
        $this->app->singleton(GuidelineProviderInterface::class, BoostGuidelineService::class);
        $this->app->singleton(BoostGuidelineService::class);
        $this->app->singleton(BoostIntegrationInterface::class, DomainBoostIntegrationService::class);
        $this->app->singleton(BoostGuidelineManager::class);
        $this->app->singleton(BoostContextProvider::class);

        // Register application services
        $this->app->singleton(McpRegistryService::class);
        $this->app->singleton(BoostIntegrationService::class);

        // Register Boost integration if enabled
        if ($this->app['config']->get('emeq-mcp.boost.enabled', false)) {
            $this->registerBoostIntegration();
        }
    }

    protected function registerBoostIntegration(): void
    {
        $boostService = $this->app->make(BoostIntegrationInterface::class);
        $boostService->initialize();
    }
}
