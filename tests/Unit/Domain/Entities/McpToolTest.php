<?php

use Emeq\McpLaravel\Domain\Mcp\Entities\McpTool;
use Emeq\McpLaravel\Domain\Mcp\ValueObjects\ToolSchema;

test('mcp tool can be created', function () {
    $schema = new ToolSchema([
        'type' => 'object',
        'properties' => ['name' => ['type' => 'string']],
    ]);

    $tool = new McpTool('test-tool', 'Test tool', $schema);

    expect($tool->getName())->toBe('test-tool')
        ->and($tool->getDescription())->toBe('Test tool')
        ->and($tool->getInputSchema())->toBeInstanceOf(ToolSchema::class);
});

test('mcp tool can be converted to array', function () {
    $schema = new ToolSchema([
        'type' => 'object',
        'properties' => ['name' => ['type' => 'string']],
    ]);

    $tool = new McpTool('test-tool', 'Test tool', $schema);

    $array = $tool->toArray();

    expect($array)->toBeArray()
        ->and($array['name'])->toBe('test-tool')
        ->and($array['description'])->toBe('Test tool')
        ->and($array)->toHaveKey('inputSchema');
});

test('mcp tool can have handler', function () {
    $schema = new ToolSchema(['type' => 'object']);
    $tool = new McpTool('test-tool', 'Test tool', $schema, 'App\\Handlers\\TestHandler');

    expect($tool->getHandler())->toBe('App\\Handlers\\TestHandler');
});
