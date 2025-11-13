<?php

namespace App\Mcp\Prompts;

use Emeq\McpLaravel\Domain\Mcp\ValueObjects\PromptTemplate;
use Emeq\McpLaravel\Infrastructure\Mcp\BasePrompt;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

/**
 * Example Invoice Management Prompt
 *
 * This prompt provides assistance with invoice management tasks,
 * including creating, updating, and managing invoices.
 */
final class InvoiceManagementPrompt extends BasePrompt
{
    public function getName(): string
    {
        return 'invoice-management';
    }

    public function getDescription(): string
    {
        return 'Get assistance with invoice management tasks, including creating, updating, and managing invoices.';
    }

    public function getArguments(): array
    {
        return [
            'task' => [
                'type'        => 'string',
                'description' => 'The invoice management task (create, update, send, cancel, etc.)',
            ],
            'context' => [
                'type'        => 'string',
                'description' => 'Additional context or requirements for the task',
            ],
        ];
    }

    public function handle(Request $request): Response
    {
        $arguments = $this->validateArguments($request->arguments());
        $template  = $this->getTemplate();

        $rendered = $template->render([
            'task'    => $arguments['task'] ?? 'invoice management',
            'context' => $arguments['context'] ?? 'No specific context provided',
        ]);

        return Response::text($rendered);
    }

    protected function getTemplate(): PromptTemplate
    {
        return new PromptTemplate(
            "You are assisting with invoice management in a Laravel application.\n\n" .
            "Task: {{task}}\n\n" .
            "Context: {{context}}\n\n" .
            "The Invoice model has the following key features:\n" .
            "- Invoice numbers are formatted as INV-YYYY-SEQ (e.g., INV-2025-001)\n" .
            "- Status values: draft, sent, paid, overdue, cancelled\n" .
            "- Relationships: customer, items (line items), payments\n" .
            "- Financial fields: subtotal, tax, total, paid_amount, balance\n" .
            "- Dates: issue_date (when invoice was created), due_date (payment deadline)\n\n" .
            "Provide clear, accurate guidance following Laravel best practices."
        );
    }
}
