<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp\Builders;

use Emeq\McpLaravel\Domain\Mcp\Contracts\McpBuilderInterface;
use Emeq\McpLaravel\Domain\Mcp\ValueObjects\PromptTemplate;
use Emeq\McpLaravel\Infrastructure\Mcp\BasePrompt;

final class PromptBuilder implements McpBuilderInterface
{
    private string $name;

    private string $description;

    private array $arguments = [];

    private ?string $template = null;

    private ?string $handler = null;

    /**
     * Set the prompt name.
     */
    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the prompt description.
     */
    public function description(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Set the prompt arguments.
     *
     * @param  array<string, mixed>  $arguments
     */
    public function arguments(array $arguments): self
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * Set the prompt template.
     */
    public function template(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Set the handler class.
     */
    public function handler(string $handler): self
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * Build and return a prompt instance.
     */
    public function build(): BasePrompt
    {
        $promptTemplate = $this->template ? new PromptTemplate($this->template) : null;

        return new class($this->name, $this->description, $this->arguments, $promptTemplate, $this->handler) extends BasePrompt
        {
            public function __construct(
                private readonly string $name,
                private readonly string $description,
                private readonly array $arguments,
                private readonly ?PromptTemplate $template,
                private readonly ?string $handler
            ) {}

            public function getName(): string
            {
                return $this->name;
            }

            public function getDescription(): string
            {
                return $this->description;
            }

            public function getArguments(): array
            {
                return $this->arguments;
            }

            protected function getTemplate(): ?PromptTemplate
            {
                return $this->template;
            }

            public function handle(\Laravel\Mcp\Request $request): \Laravel\Mcp\Response
            {
                if ($this->handler) {
                    $handler = app($this->handler);

                    return $handler->handle($request);
                }

                $template = $this->getTemplate();
                if ($template) {
                    $rendered = $template->render($request->arguments());

                    return \Laravel\Mcp\Response::text($rendered);
                }

                return \Laravel\Mcp\Response::text('Prompt executed successfully.');
            }
        };
    }
}
