<?php

use Emeq\McpLaravel\Application\Services\McpRegistryService;

test('mcp registry service can get registered components', function () {
    $service = app(McpRegistryService::class);

    $components = $service->getRegisteredComponents();

    expect($components)->toBeArray()
        ->and($components)->toHaveKeys(['tools', 'resources', 'prompts']);
});

test('mcp registry service lists all pre-built tools', function () {
    $service = app(McpRegistryService::class);

    $components = $service->getRegisteredComponents();

    expect($components['tools'])->toBeArray()
        ->and($components['tools'])->not->toBeEmpty();
});

test('mcp registry service lists all pre-built resources', function () {
    $service = app(McpRegistryService::class);

    $components = $service->getRegisteredComponents();

    expect($components['resources'])->toBeArray()
        ->and($components['resources'])->not->toBeEmpty();
});

test('mcp registry service lists all pre-built prompts', function () {
    $service = app(McpRegistryService::class);

    $components = $service->getRegisteredComponents();

    expect($components['prompts'])->toBeArray()
        ->and($components['prompts'])->not->toBeEmpty();
});

