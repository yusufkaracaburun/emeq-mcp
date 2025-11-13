<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp;

use Emeq\McpLaravel\Domain\Mcp\Contracts\PromptInterface;
use Emeq\McpLaravel\Domain\Mcp\ValueObjects\PromptTemplate;
use Exception;
use Illuminate\Support\Facades\Validator;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Prompt;

abstract class BasePrompt extends Prompt implements PromptInterface
{
    /**
     * Get the prompt name.
     */
    abstract public function getName(): string;

    /**
     * Get the prompt description.
     */
    abstract public function getDescription(): string;

    /**
     * Get the prompt arguments schema.
     *
     * @return array<string, mixed>
     */
    abstract public function getArguments(): array;

    /**
     * Handle the prompt request.
     */
    abstract public function handle(Request $request): Response;

    /**
     * Get the prompt template.
     */
    protected function getTemplate(): ?PromptTemplate
    {
        return null;
    }

    /**
     * Validate the prompt arguments.
     *
     * @param  array<string, mixed>  $arguments
     * @return array<string, mixed>
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateArguments(array $arguments): array
    {
        $schema = $this->getArguments();
        $rules  = $this->convertSchemaToRules($schema);

        return Validator::make($arguments, $rules)->validate();
    }

    /**
     * Convert JSON schema to Laravel validation rules.
     *
     * @param  array<string, mixed>  $schema
     * @return array<string, string>
     */
    protected function convertSchemaToRules(array $schema): array
    {
        $rules = [];

        foreach ($schema as $key => $property) {
            $rule = [];

            if (isset($property['type'])) {
                $rule[] = $this->mapTypeToRule($property['type']);
            }

            if (isset($property['required']) && $property['required']) {
                $rule[] = 'required';
            } else {
                $rule[] = 'nullable';
            }

            $rules[$key] = implode('|', $rule);
        }

        return $rules;
    }

    /**
     * Map JSON schema type to Laravel validation rule.
     */
    protected function mapTypeToRule(string $type): string
    {
        return match ($type) {
            'string' => 'string',
            'number', 'integer' => 'numeric',
            'boolean' => 'boolean',
            'array'   => 'array',
            'object'  => 'array',
            default   => 'string',
        };
    }

    /**
     * Get Boost guidelines for a specific context.
     *
     * @return array<string, mixed>
     */
    protected function getBoostGuidelines(?string $context = null): array
    {
        if ( ! config('emeq-mcp.boost.enabled', false)) {
            return [];
        }

        try {
            $boostService = app(\Emeq\McpLaravel\Application\Services\BoostIntegrationService::class);

            return $context
                ? $boostService->getGuidelinesForContext($context)
                : $boostService->getGuidelines();
        } catch (Exception $e) {
            // If Boost is not available, return empty array
            return [];
        }
    }

    /**
     * Format Boost guidelines as a string for inclusion in prompts.
     */
    protected function formatBoostGuidelines(array $guidelines): string
    {
        if (empty($guidelines)) {
            return '';
        }

        $formatted = "\n\n## Project Guidelines\n\n";
        $formatted .= "Please follow these project-specific guidelines:\n\n";

        foreach ($guidelines as $guideline) {
            if (is_array($guideline)) {
                if (isset($guideline['title'])) {
                    $formatted .= "### {$guideline['title']}\n\n";
                }

                if (isset($guideline['content'])) {
                    $formatted .= $guideline['content'] . "\n\n";
                } elseif (isset($guideline['description'])) {
                    $formatted .= $guideline['description'] . "\n\n";
                }
            } else {
                $formatted .= "- {$guideline}\n";
            }
        }

        return $formatted;
    }
}
