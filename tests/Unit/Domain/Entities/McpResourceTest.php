<?php

use Emeq\McpLaravel\Domain\Mcp\Entities\McpResource;
use Emeq\McpLaravel\Domain\Mcp\ValueObjects\ResourceUri;

test('mcp resource can be created', function () {
    $uri = new ResourceUri('laravel://test/resource');
    $resource = new McpResource($uri, 'Test Resource', 'Test description', 'application/json');

    expect($resource->getName())->toBe('Test Resource')
        ->and($resource->getDescription())->toBe('Test description')
        ->and($resource->getMimeType())->toBe('application/json')
        ->and($resource->getUri())->toBeInstanceOf(ResourceUri::class);
});

test('mcp resource can be converted to array', function () {
    $uri = new ResourceUri('laravel://test/resource');
    $resource = new McpResource($uri, 'Test Resource', 'Test description', 'application/json');

    $array = $resource->toArray();

    expect($array)->toBeArray()
        ->and($array['name'])->toBe('Test Resource')
        ->and($array['uri'])->toBe('laravel://test/resource')
        ->and($array['mimeType'])->toBe('application/json');
});

