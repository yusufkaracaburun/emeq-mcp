<?php

use Emeq\McpLaravel\Infrastructure\Mcp\Builders\ToolBuilder;

test('tool builder can create tool with fluent api', function () {
    $tool = (new ToolBuilder)
        ->name('test-tool')
        ->description('Test tool description')
        ->inputSchema([
            'type' => 'object',
            'properties' => ['name' => ['type' => 'string']],
        ])
        ->build();

    expect($tool->getName())->toBe('test-tool')
        ->and($tool->getDescription())->toBe('Test tool description')
        ->and($tool->getInputSchema())->toBeArray();
});

test('tool builder can set handler', function () {
    $tool = (new ToolBuilder)
        ->name('test-tool')
        ->description('Test')
        ->inputSchema(['type' => 'object'])
        ->handler('App\\Handlers\\TestHandler')
        ->build();

    // Handler is set internally, tool should be buildable
    expect($tool)->toBeInstanceOf(\Emeq\McpLaravel\Infrastructure\Mcp\BaseTool::class);
});
