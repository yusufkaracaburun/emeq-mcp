<?php

use Emeq\McpLaravel\Infrastructure\Mcp\Resources\ConfigResource;
use Laravel\Mcp\Request;

test('config resource can get config value', function () {
    $resource = new ConfigResource;
    $request = \Mockery::mock(Request::class);
    $request->shouldReceive('uri')->andReturn('laravel://config/app.name');

    $response = $resource->handle($request);

    expect($response)->toBeInstanceOf(\Laravel\Mcp\Response::class);
});

test('config resource returns error when disabled', function () {
    config()->set('emeq-mcp.resources.config.enabled', false);

    $resource = new ConfigResource;
    $request = \Mockery::mock(Request::class);
    $request->shouldReceive('uri')->andReturn('laravel://config/app.name');

    $response = $resource->handle($request);

    expect($response)->toBeInstanceOf(\Laravel\Mcp\Response::class);
});
