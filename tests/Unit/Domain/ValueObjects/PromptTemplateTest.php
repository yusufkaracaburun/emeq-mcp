<?php

use Emeq\McpLaravel\Domain\Mcp\ValueObjects\PromptTemplate;

test('prompt template can be created and rendered', function () {
    $template = new PromptTemplate('Hello {{name}}', ['name' => 'World']);

    expect($template->getTemplate())->toBe('Hello {{name}}')
        ->and($template->render())->toBe('Hello World');
});

test('prompt template can render with additional variables', function () {
    $template = new PromptTemplate('Hello {{name}}, you are {{age}} years old');

    $rendered = $template->render(['name' => 'John', 'age' => '30']);

    expect($rendered)->toBe('Hello John, you are 30 years old');
});

test('prompt template throws exception when empty', function () {
    expect(fn () => new PromptTemplate(''))
        ->toThrow(InvalidArgumentException::class, 'Prompt template cannot be empty');
});

test('prompt template supports both syntaxes', function () {
    $template = new PromptTemplate('Hello {{name}} and {{ other }}');

    $rendered = $template->render(['name' => 'John', 'other' => 'Jane']);

    expect($rendered)->toContain('John')->toContain('Jane');
});

