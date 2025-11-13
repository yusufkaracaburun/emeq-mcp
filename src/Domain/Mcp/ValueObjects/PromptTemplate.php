<?php

namespace Emeq\McpLaravel\Domain\Mcp\ValueObjects;

use InvalidArgumentException;

final class PromptTemplate
{
    /**
     * @param  array<string, mixed>  $variables
     */
    public function __construct(
        private readonly string $template,
        private readonly array $variables = []
    ) {
        $this->validate();
    }

    /**
     * Render the template with variables.
     */
    public function render(?array $additionalVariables = null): string
    {
        $vars = array_merge($this->variables, $additionalVariables ?? []);

        $rendered = $this->template;

        foreach ($vars as $key => $value) {
            $rendered = str_replace("{{{$key}}}", (string) $value, $rendered);
            $rendered = str_replace("{{ $key }}", (string) $value, $rendered);
        }

        return $rendered;
    }

    /**
     * Get the template string.
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * Get the variables.
     *
     * @return array<string, mixed>
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * Validate the template.
     *
     * @throws InvalidArgumentException
     */
    private function validate(): void
    {
        if (empty(trim($this->template))) {
            throw new InvalidArgumentException('Prompt template cannot be empty.');
        }
    }
}
