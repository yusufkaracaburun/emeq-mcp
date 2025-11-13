<?php

use Emeq\McpLaravel\Domain\Mcp\ValueObjects\ToolSchema;

test('tool schema can be created with valid schema', function () {
    $schema = new ToolSchema([
        'type' => 'object',
        'properties' => [
            'name' => ['type' => 'string'],
        ],
    ]);

    expect($schema->toArray())->toBeArray()
        ->and($schema->hasProperty('type'))->toBeTrue();
});

test('tool schema throws exception when empty', function () {
    expect(fn () => new ToolSchema([]))
        ->toThrow(InvalidArgumentException::class, 'Tool schema cannot be empty');
});

test('tool schema throws exception when missing type and properties', function () {
    expect(fn () => new ToolSchema(['invalid' => 'data']))
        ->toThrow(InvalidArgumentException::class);
});

test('tool schema can get property', function () {
    $schema = new ToolSchema([
        'type' => 'object',
        'properties' => ['name' => ['type' => 'string']],
    ]);

    expect($schema->getProperty('type'))->toBe('object')
        ->and($schema->getProperty('nonexistent', 'default'))->toBe('default');
});

