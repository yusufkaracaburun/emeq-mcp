<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp\Prompts;

use Emeq\McpLaravel\Domain\Mcp\ValueObjects\PromptTemplate;
use Emeq\McpLaravel\Infrastructure\Mcp\BasePrompt;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

final class DatabaseDesignPrompt extends BasePrompt
{
    public function getName(): string
    {
        return 'database-design';
    }

    public function getDescription(): string
    {
        return 'Get assistance with database design, migrations, and schema planning for Laravel applications.';
    }

    public function getArguments(): array
    {
        return [
            'requirements' => [
                'type' => 'string',
                'description' => 'Database requirements and specifications',
            ],
            'existing_schema' => [
                'type' => 'string',
                'description' => 'Existing database schema (optional)',
            ],
            'relationships' => [
                'type' => 'string',
                'description' => 'Required relationships between entities',
            ],
        ];
    }

    protected function getTemplate(): PromptTemplate
    {
        return new PromptTemplate(
            "Help me design a database schema for Laravel:\n\nRequirements:\n{{requirements}}\n\n{{#relationships}}Relationships:\n{{relationships}}\n{{/relationships}}\n\n{{#existing_schema}}Existing Schema:\n{{existing_schema}}\n{{/existing_schema}}\n\nPlease provide:\n1. Table structure with columns and types\n2. Migration code\n3. Model relationships\n4. Indexes and constraints\n5. Best practices recommendations"
        );
    }

    public function handle(Request $request): Response
    {
        if (! config('emeq-mcp.prompts.database_design.enabled', true)) {
            return Response::error('Database design prompt is disabled.');
        }

        $arguments = $this->validateArguments($request->arguments());
        $template = $this->getTemplate();

        $rendered = $template->render([
            'requirements' => $arguments['requirements'] ?? 'No requirements specified',
            'relationships' => $arguments['relationships'] ?? '',
            'existing_schema' => $arguments['existing_schema'] ?? '',
        ]);

        return Response::text($rendered);
    }
}
