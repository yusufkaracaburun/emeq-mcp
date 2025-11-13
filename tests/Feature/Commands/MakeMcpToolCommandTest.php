<?php

use Emeq\McpLaravel\Application\Commands\MakeMcpToolCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

test('make mcp tool command can generate tool', function () {
    $toolPath = app_path('Mcp/Tools/TestTool.php');

    // Clean up if exists
    if (File::exists($toolPath)) {
        File::delete($toolPath);
    }

    Artisan::call(MakeMcpToolCommand::class, ['name' => 'Test']);

    expect(File::exists($toolPath))->toBeTrue();

    // Cleanup
    if (File::exists($toolPath)) {
        File::delete($toolPath);
    }
});

