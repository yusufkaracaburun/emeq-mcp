<?php

use Emeq\McpLaravel\Domain\Mcp\Entities\McpServer;

test('mcp server can be created', function () {
    $server = new McpServer('Test Server', '1.0.0', 'Test instructions');

    expect($server->getName())->toBe('Test Server')
        ->and($server->getVersion())->toBe('1.0.0')
        ->and($server->getInstructions())->toBe('Test instructions');
});

test('mcp server can register tools', function () {
    $server = new McpServer('Test Server', '1.0.0', 'Test instructions');

    $server->registerTool('App\\Tools\\TestTool');

    expect($server->getTools())->toContain('App\\Tools\\TestTool');
});

test('mcp server can register resources', function () {
    $server = new McpServer('Test Server', '1.0.0', 'Test instructions');

    $server->registerResource('App\\Resources\\TestResource');

    expect($server->getResources())->toContain('App\\Resources\\TestResource');
});

test('mcp server can register prompts', function () {
    $server = new McpServer('Test Server', '1.0.0', 'Test instructions');

    $server->registerPrompt('App\\Prompts\\TestPrompt');

    expect($server->getPrompts())->toContain('App\\Prompts\\TestPrompt');
});

test('mcp server prevents duplicate registrations', function () {
    $server = new McpServer('Test Server', '1.0.0', 'Test instructions');

    $server->registerTool('App\\Tools\\TestTool');
    $server->registerTool('App\\Tools\\TestTool');

    expect($server->getTools())->toHaveCount(1);
});

test('mcp server can check if it has components', function () {
    $server = new McpServer('Test Server', '1.0.0', 'Test instructions');

    expect($server->hasComponents())->toBeFalse();

    $server->registerTool('App\\Tools\\TestTool');

    expect($server->hasComponents())->toBeTrue();
});

