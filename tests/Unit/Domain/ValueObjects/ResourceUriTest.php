<?php

use Emeq\McpLaravel\Domain\Mcp\ValueObjects\ResourceUri;

test('resource uri can be created with valid uri', function () {
    $uri = new ResourceUri('laravel://model/User');

    expect($uri->toString())->toBe('laravel://model/User')
        ->and($uri->isValid())->toBeTrue();
});

test('resource uri can be created with http url', function () {
    $uri = new ResourceUri('https://example.com/resource');

    expect($uri->toString())->toBe('https://example.com/resource')
        ->and($uri->getScheme())->toBe('https')
        ->and($uri->isValid())->toBeTrue();
});

test('resource uri throws exception when empty', function () {
    expect(fn () => new ResourceUri(''))
        ->toThrow(InvalidArgumentException::class, 'Resource URI cannot be empty');
});

test('resource uri throws exception when invalid', function () {
    expect(fn () => new ResourceUri('invalid-uri'))
        ->toThrow(InvalidArgumentException::class);
});

test('resource uri can extract path', function () {
    $uri = new ResourceUri('laravel://model/User');

    expect($uri->getPath())->toBeString();
});
