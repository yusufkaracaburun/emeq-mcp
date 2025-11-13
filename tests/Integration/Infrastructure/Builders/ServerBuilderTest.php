<?php

use Emeq\McpLaravel\Infrastructure\Mcp\Builders\ServerBuilder;

test('server builder can create server with fluent api', function () {
    $server = (new ServerBuilder())
        ->name('Test Server')
        ->version('2.0.0')
        ->instructions('Test instructions')
        ->withTool('App\\Tools\\TestTool')
        ->build();

    expect($server->getName())->toBe('Test Server')
        ->and($server->getVersion())->toBe('2.0.0')
        ->and($server->getInstructions())->toBe('Test instructions');
});

test('server builder can add multiple tools', function () {
    $server = (new ServerBuilder())
        ->withTools(['App\\Tools\\Tool1', 'App\\Tools\\Tool2'])
        ->build();

    expect($server->getTools())->toHaveCount(2);
});

test('server builder can add multiple resources', function () {
    $server = (new ServerBuilder())
        ->withResources(['App\\Resources\\Resource1', 'App\\Resources\\Resource2'])
        ->build();

    expect($server->getResources())->toHaveCount(2);
});

test('server builder can add multiple prompts', function () {
    $server = (new ServerBuilder())
        ->withPrompts(['App\\Prompts\\Prompt1', 'App\\Prompts\\Prompt2'])
        ->build();

    expect($server->getPrompts())->toHaveCount(2);
});

