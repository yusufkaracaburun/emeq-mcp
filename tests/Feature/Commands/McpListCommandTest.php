<?php

use Emeq\McpLaravel\Application\Commands\McpListCommand;
use Illuminate\Support\Facades\Artisan;

test('mcp list command can be executed', function () {
    Artisan::call(McpListCommand::class);

    expect(Artisan::output())->toContain('Registered MCP Components:');
});

test('mcp list command shows tools', function () {
    Artisan::call(McpListCommand::class);

    expect(Artisan::output())->toContain('Tools:');
});

test('mcp list command shows resources', function () {
    Artisan::call(McpListCommand::class);

    expect(Artisan::output())->toContain('Resources:');
});

test('mcp list command shows prompts', function () {
    Artisan::call(McpListCommand::class);

    expect(Artisan::output())->toContain('Prompts:');
});

