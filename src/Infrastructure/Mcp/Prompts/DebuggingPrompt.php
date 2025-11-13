<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp\Prompts;

use Emeq\McpLaravel\Domain\Mcp\ValueObjects\PromptTemplate;
use Emeq\McpLaravel\Infrastructure\Mcp\BasePrompt;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

final class DebuggingPrompt extends BasePrompt
{
    public function getName(): string
    {
        return 'debugging';
    }

    public function getDescription(): string
    {
        return 'Get assistance with debugging Laravel applications, including error analysis and troubleshooting.';
    }

    public function getArguments(): array
    {
        return [
            'error_message' => [
                'type' => 'string',
                'description' => 'The error message or exception',
            ],
            'context' => [
                'type' => 'string',
                'description' => 'Additional context about the issue',
            ],
            'code_snippet' => [
                'type' => 'string',
                'description' => 'Relevant code snippet (optional)',
            ],
        ];
    }

    public function handle(Request $request): Response
    {
        if (! config('emeq-mcp.prompts.debugging.enabled', true)) {
            return Response::error('Debugging prompt is disabled.');
        }

        $arguments = $this->validateArguments($request->arguments());
        $template = $this->getTemplate();

        // Get Boost guidelines for debugging context
        $guidelines = $this->getBoostGuidelines('debugging');
        $guidelinesText = $this->formatBoostGuidelines($guidelines);

        $rendered = $template->render([
            'error_message' => $arguments['error_message'] ?? 'No error message provided',
            'context' => $arguments['context'] ?? 'No additional context',
            'code_snippet' => $arguments['code_snippet'] ?? '',
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
            "Help me debug this Laravel issue:\n\nError: {{error_message}}\n\nContext: {{context}}\n\n{{#code_snippet}}Code:\n{{code_snippet}}\n{{/code_snippet}}\n\nPlease provide:\n1. Analysis of the error\n2. Possible causes\n3. Step-by-step solution\n4. Prevention tips"
        );
    }
}
