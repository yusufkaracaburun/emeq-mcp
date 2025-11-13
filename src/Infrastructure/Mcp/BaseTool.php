<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp;

use Emeq\McpLaravel\Domain\Mcp\Contracts\ToolInterface;
use Illuminate\Support\Facades\Validator;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

abstract class BaseTool extends Tool implements ToolInterface
{
    /**
     * Get the tool name.
     */
    abstract public function getName(): string;

    /**
     * Get the tool description.
     */
    abstract public function getDescription(): string;

    /**
     * Get the tool input schema.
     *
     * @return array<string, mixed>
     */
    abstract public function getInputSchema(): array;

    /**
     * Handle the tool request.
     */
    abstract public function handle(Request $request): Response;

    /**
     * Validate the tool arguments.
     *
     * @param  array<string, mixed>  $arguments
     * @return array<string, mixed>
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateArguments(array $arguments): array
    {
        $schema = $this->getInputSchema();
        $rules = $this->convertSchemaToRules($schema);

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

        if (isset($schema['properties'])) {
            foreach ($schema['properties'] as $key => $property) {
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
            'array' => 'array',
            'object' => 'array',
            default => 'string',
        };
    }
}
