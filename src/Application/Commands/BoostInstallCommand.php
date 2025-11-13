<?php

namespace Emeq\McpLaravel\Application\Commands;

use Illuminate\Console\Command;

final class BoostInstallCommand extends Command
{
    protected $signature = 'mcp:boost-install';

    protected $description = 'Install and configure Boost integration for MCP';

    public function handle(): int
    {
        $this->info('Installing Boost integration...');

        // Create guidelines directory
        $guidelinesPath = base_path('.boost/guidelines');
        if (! is_dir($guidelinesPath)) {
            mkdir($guidelinesPath, 0755, true);
            $this->info("Created guidelines directory: {$guidelinesPath}");
        }

        // Update config
        $this->updateConfig();

        $this->info('Boost integration installed successfully!');
        $this->info('You can now add guidelines to: '.$guidelinesPath);

        return self::SUCCESS;
    }

    protected function updateConfig(): void
    {
        $configPath = config_path('emeq-mcp.php');

        if (! file_exists($configPath)) {
            $this->warn('Config file not found. Please publish the config first: php artisan vendor:publish --tag=emeq-mcp-config');

            return;
        }

        $config = file_get_contents($configPath);

        // Enable Boost if not already enabled
        if (! str_contains($config, "'enabled' => true")) {
            $config = str_replace(
                "'enabled' => env('EMEQ_MCP_BOOST_ENABLED', false),",
                "'enabled' => env('EMEQ_MCP_BOOST_ENABLED', true),",
                $config
            );
            file_put_contents($configPath, $config);
            $this->info('Boost enabled in configuration.');
        }
    }
}
