<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp\Prompts;

use Emeq\McpLaravel\Domain\Mcp\ValueObjects\PromptTemplate;
use Emeq\McpLaravel\Infrastructure\Mcp\BasePrompt;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

final class CodeGenerationPrompt extends BasePrompt
{
    public function getName(): string
    {
        return 'code-generation';
    }

    public function getDescription(): string
    {
        return 'Get assistance with generating Laravel code following best practices and conventions.';
    }

    public function getArguments(): array
    {
        return [
            'type' => [
                'type' => 'string',
                'description' => 'Type of code to generate (controller, model, migration, etc.)',
            ],
            'name' => [
                'type' => 'string',
                'description' => 'Name of the component to generate',
            ],
            'requirements' => [
                'type' => 'string',
                'description' => 'Additional requirements or specifications',
            ],
        ];
    }

    public function handle(Request $request): Response
    {
        if (! config('emeq-mcp.prompts.code_generation.enabled', true)) {
            return Response::error('Code generation prompt is disabled.');
        }

        $arguments = $this->validateArguments($request->arguments());
        $template = $this->getTemplate();

        // Get Boost guidelines for code generation context
        $guidelines = $this->getBoostGuidelines('code-generation');
        $guidelinesText = $this->formatBoostGuidelines($guidelines);

        $rendered = $template->render([
            'type' => $arguments['type'] ?? 'component',
            'name' => $arguments['name'] ?? 'Component',
            'requirements' => $arguments['requirements'] ?? 'No specific requirements',
        ]);

        // Append Boost guidelines to the rendered prompt
        if (! empty($guidelinesText)) {
            $rendered .= $guidelinesText;
        }

        return Response::text($rendered);
    }

    protected function getTemplate(): PromptTemplate
    {
        return new PromptTemplate(
            "Generate Laravel {{type}} code for '{{name}}'.\n\nRequirements:\n{{requirements}}\n\nFollow Laravel best practices, use proper namespacing, and include appropriate validation and error handling."
        );
    }
}
