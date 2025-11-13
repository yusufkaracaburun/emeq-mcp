<?php

use Emeq\McpLaravel\Application\Commands\BoostInstallCommand;
use Illuminate\Support\Facades\Artisan;

test('boost install command can be executed', function () {
    Artisan::call(BoostInstallCommand::class);

    expect(Artisan::output())->toContain('Installing Boost integration');
});

test('boost install command creates guidelines directory', function () {
    $guidelinesPath = base_path('.boost/guidelines');

    // Clean up if exists
    if (is_dir($guidelinesPath)) {
        rmdir($guidelinesPath);
    }

    Artisan::call(BoostInstallCommand::class);

    expect(is_dir($guidelinesPath))->toBeTrue();

    // Cleanup
    if (is_dir($guidelinesPath)) {
        rmdir($guidelinesPath);
    }
});
