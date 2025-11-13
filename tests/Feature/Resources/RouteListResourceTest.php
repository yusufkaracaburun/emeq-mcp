<?php

use Emeq\McpLaravel\Infrastructure\Mcp\Resources\RouteListResource;
use Laravel\Mcp\Request;

test('route list resource can get routes', function () {
    $resource = new RouteListResource;
    $request = \Mockery::mock(Request::class);
    $request->shouldReceive('uri')->andReturn('laravel://routes');

    $response = $resource->handle($request);

    expect($response)->toBeInstanceOf(\Laravel\Mcp\Response::class);
});

test('route list resource returns error when disabled', function () {
    config()->set('emeq-mcp.resources.route_list.enabled', false);

    $resource = new RouteListResource;
    $request = \Mockery::mock(Request::class);
    $request->shouldReceive('uri')->andReturn('laravel://routes');

    $response = $resource->handle($request);

    expect($response)->toBeInstanceOf(\Laravel\Mcp\Response::class);
});
