<?php

namespace Emeq\McpLaravel\Application\Commands;

use Emeq\McpLaravel\Application\Services\McpRegistryService;
use Illuminate\Console\Command;

final class McpListCommand extends Command
{
    protected $signature = 'mcp:list';

    protected $description = 'List all registered MCP components';

    public function __construct(
        private readonly McpRegistryService $registryService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $components = $this->registryService->getRegisteredComponents();

        $this->info('Registered MCP Components:');
        $this->newLine();

        $this->info('Tools:');
        foreach ($components['tools'] as $tool) {
            $this->line("  - {$tool}");
        }

        $this->newLine();
        $this->info('Resources:');
        foreach ($components['resources'] as $resource) {
            $this->line("  - {$resource}");
        }

        $this->newLine();
        $this->info('Prompts:');
        foreach ($components['prompts'] as $prompt) {
            $this->line("  - {$prompt}");
        }

        return self::SUCCESS;
    }
}
