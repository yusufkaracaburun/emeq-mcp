<?php

use Emeq\McpLaravel\Domain\Mcp\Entities\McpPrompt;
use Emeq\McpLaravel\Domain\Mcp\ValueObjects\PromptTemplate;

test('mcp prompt can be created', function () {
    $prompt = new McpPrompt(
        'test-prompt',
        'Test prompt',
        ['name' => ['type' => 'string']]
    );

    expect($prompt->getName())->toBe('test-prompt')
        ->and($prompt->getDescription())->toBe('Test prompt')
        ->and($prompt->getArguments())->toBeArray();
});

test('mcp prompt can have template', function () {
    $template = new PromptTemplate('Hello {{name}}');
    $prompt = new McpPrompt(
        'test-prompt',
        'Test prompt',
        ['name' => ['type' => 'string']],
        $template
    );

    expect($prompt->getTemplate())->toBeInstanceOf(PromptTemplate::class);
});

test('mcp prompt can be converted to array', function () {
    $prompt = new McpPrompt(
        'test-prompt',
        'Test prompt',
        ['name' => ['type' => 'string']]
    );

    $array = $prompt->toArray();

    expect($array)->toBeArray()
        ->and($array['name'])->toBe('test-prompt')
        ->and($array)->toHaveKey('arguments');
});
