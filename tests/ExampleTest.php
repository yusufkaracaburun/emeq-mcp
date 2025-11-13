<?php

test('package is loaded correctly', function () {
    expect(class_exists(\Emeq\McpLaravel\EmeqMcpLaravelServiceProvider::class))->toBeTrue();
});

test('service provider is registered', function () {
    $providers = app()->getLoadedProviders();

    expect($providers)->toHaveKey(\Emeq\McpLaravel\EmeqMcpLaravelServiceProvider::class);
});
