<?php

use Emeq\McpLaravel\Application\Commands\MakeMcpServerCommand;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

test('make mcp server command can generate server', function () {
    $serverPath = app_path('Mcp/Servers/TestServer.php');

    // Clean up if exists
    if (File::exists($serverPath)) {
        File::delete($serverPath);
    }

    Artisan::call(MakeMcpServerCommand::class, ['name' => 'Test']);

    expect(Artisan::output())->not->toContain('already exists')
        ->and(File::exists($serverPath))->toBeTrue();

    // Cleanup
    if (File::exists($serverPath)) {
        File::delete($serverPath);
    }
});

test('make mcp server command fails if server exists', function () {
    $serverPath = app_path('Mcp/Servers/TestServer.php');

    // Create the directory and file first
    $dir = dirname($serverPath);
    if (! File::exists($dir)) {
        File::makeDirectory($dir, 0755, true);
    }
    if (! File::exists($serverPath)) {
        File::put($serverPath, '<?php');
    }

    Artisan::call(MakeMcpServerCommand::class, ['name' => 'Test']);

    expect(Artisan::output())->toContain('already exists');

    // Cleanup
    if (File::exists($serverPath)) {
        File::delete($serverPath);
    }
});
