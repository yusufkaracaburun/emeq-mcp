<?php

use Emeq\McpLaravel\Domain\Boost\Contracts\GuidelineProviderInterface;
use Emeq\McpLaravel\Domain\Boost\Services\BoostIntegrationService;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Mockery\MockInterface;

test('boost integration service checks if enabled', function () {
    $config = Mockery::mock(ConfigRepository::class);
    $config->shouldReceive('get')->with('emeq-mcp.boost.enabled', false)->andReturn(true);
    $config->shouldReceive('get')->with('emeq-mcp.boost.guidelines_path')->andReturn(null);

    $guidelineProvider = Mockery::mock(GuidelineProviderInterface::class);

    $service = new BoostIntegrationService($guidelineProvider, $config);

    expect($service->isEnabled())->toBeTrue();
});

test('boost integration service initializes when enabled', function () {
    $config = Mockery::mock(ConfigRepository::class);
    $config->shouldReceive('get')->with('emeq-mcp.boost.enabled', false)->andReturn(true);
    $config->shouldReceive('get')->with('emeq-mcp.boost.guidelines_path')->andReturn(null);

    $guidelineProvider = Mockery::mock(GuidelineProviderInterface::class);

    $service = new BoostIntegrationService($guidelineProvider, $config);

    $service->initialize();

    // Should not throw exception
    expect(true)->toBeTrue();
});

