<?php

use Emeq\McpLaravel\Infrastructure\Mcp\Prompts\CodeGenerationPrompt;
use Laravel\Mcp\Request;

test('code generation prompt can handle request', function () {
    $prompt = new CodeGenerationPrompt;
    $request = \Mockery::mock(Request::class);
    $request->shouldReceive('arguments')->andReturn([
        'type' => 'controller',
        'name' => 'UserController',
        'requirements' => 'CRUD operations',
    ]);

    $response = $prompt->handle($request);

    expect($response)->toBeInstanceOf(\Laravel\Mcp\Response::class);
});

test('code generation prompt returns error when disabled', function () {
    config()->set('emeq-mcp.prompts.code_generation.enabled', false);

    $prompt = new CodeGenerationPrompt;
    $request = \Mockery::mock(Request::class);
    $request->shouldReceive('arguments')->andReturn([
        'type' => 'controller',
        'name' => 'Test',
    ]);

    $response = $prompt->handle($request);

    expect($response)->toBeInstanceOf(\Laravel\Mcp\Response::class);
});
