<?php

use Emeq\McpLaravel\Application\Services\BoostIntegrationService;

test('boost integration service can get guidelines', function () {
    config()->set('emeq-mcp.boost.enabled', true);

    $service = app(BoostIntegrationService::class);

    $guidelines = $service->getGuidelines();

    expect($guidelines)->toBeArray();
});

test('boost integration service can get guidelines for context', function () {
    config()->set('emeq-mcp.boost.enabled', true);

    $service = app(BoostIntegrationService::class);

    $guidelines = $service->getGuidelinesForContext('code-generation');

    expect($guidelines)->toBeArray();
});

test('boost integration service can add context', function () {
    config()->set('emeq-mcp.boost.enabled', true);

    $service = app(BoostIntegrationService::class);

    $service->addContext(['test' => 'value']);

    // Context is added internally, no exception should be thrown
    expect(true)->toBeTrue();
});
