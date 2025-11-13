<?php

namespace Emeq\McpLaravel\Tests;

use Emeq\McpLaravel\EmeqMcpLaravelServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Emeq\\McpLaravel\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function tearDown(): void
    {
        \Mockery::close();

        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return [
            EmeqMcpLaravelServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('emeq-mcp.auto_register', false);
        config()->set('emeq-mcp.boost.enabled', false);
    }
}
