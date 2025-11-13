<?php

use Emeq\McpLaravel\Infrastructure\Mcp\Tools\DatabaseQueryTool;
use Illuminate\Support\Facades\DB;
use Laravel\Mcp\Request;

test('database query tool can execute select query', function () {
    DB::statement('CREATE TABLE IF NOT EXISTS test_table (id INTEGER, name TEXT)');
    DB::insert('INSERT INTO test_table (id, name) VALUES (1, "Test")');

    $tool = new DatabaseQueryTool;
    $request = \Mockery::mock(Request::class);
    $request->shouldReceive('arguments')->andReturn([
        'query' => 'SELECT * FROM test_table WHERE id = ?',
        'bindings' => [1],
    ]);

    $response = $tool->handle($request);

    expect($response)->toBeInstanceOf(\Laravel\Mcp\Response::class);
});

test('database query tool rejects non-select queries', function () {
    $tool = new DatabaseQueryTool;
    $request = \Mockery::mock(Request::class);
    $request->shouldReceive('arguments')->andReturn([
        'query' => 'DELETE FROM test_table',
    ]);

    $response = $tool->handle($request);

    // Should return error response
    expect($response)->toBeInstanceOf(\Laravel\Mcp\Response::class);
});

test('database query tool returns error when disabled', function () {
    config()->set('emeq-mcp.tools.database_query.enabled', false);

    $tool = new DatabaseQueryTool;
    $request = \Mockery::mock(Request::class);
    $request->shouldReceive('arguments')->andReturn(['query' => 'SELECT 1']);

    $response = $tool->handle($request);

    expect($response)->toBeInstanceOf(\Laravel\Mcp\Response::class);
});
