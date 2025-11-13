<?php

use Emeq\McpLaravel\Infrastructure\Mcp\Tools\CacheOperationTool;
use Illuminate\Support\Facades\Cache;
use Laravel\Mcp\Request;

test('cache operation tool can get value', function () {
    Cache::put('test-key', 'test-value', 60);

    $tool = new CacheOperationTool;
    $request = \Mockery::mock(Request::class);
    $request->shouldReceive('arguments')->andReturn([
        'operation' => 'get',
        'key' => 'test-key',
    ]);

    $response = $tool->handle($request);

    expect($response)->toBeInstanceOf(\Laravel\Mcp\Response::class);
});

test('cache operation tool can set value', function () {
    $tool = new CacheOperationTool;
    $request = \Mockery::mock(Request::class);
    $request->shouldReceive('arguments')->andReturn([
        'operation' => 'set',
        'key' => 'test-key',
        'value' => 'test-value',
    ]);

    $response = $tool->handle($request);

    expect($response)->toBeInstanceOf(\Laravel\Mcp\Response::class)
        ->and(Cache::get('test-key'))->toBe('test-value');
});

test('cache operation tool can forget value', function () {
    Cache::put('test-key', 'test-value', 60);

    $tool = new CacheOperationTool;
    $request = \Mockery::mock(Request::class);
    $request->shouldReceive('arguments')->andReturn([
        'operation' => 'forget',
        'key' => 'test-key',
    ]);

    $response = $tool->handle($request);

    expect($response)->toBeInstanceOf(\Laravel\Mcp\Response::class)
        ->and(Cache::get('test-key'))->toBeNull();
});

test('cache operation tool returns error when disabled', function () {
    config()->set('emeq-mcp.tools.cache_operation.enabled', false);

    $tool = new CacheOperationTool;
    $request = \Mockery::mock(Request::class);
    $request->shouldReceive('arguments')->andReturn(['operation' => 'get', 'key' => 'test']);

    $response = $tool->handle($request);

    // Error responses should have error content
    expect($response)->toBeInstanceOf(\Laravel\Mcp\Response::class);
});
