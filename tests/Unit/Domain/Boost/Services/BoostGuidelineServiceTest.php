<?php

use Emeq\McpLaravel\Domain\Boost\Services\BoostGuidelineService;

test('boost guideline service can add guidelines', function () {
    $service = new BoostGuidelineService();

    $service->addGuideline([
        'context' => 'code-generation',
        'rule' => 'Use type hints',
    ]);

    $guidelines = $service->getGuidelines();

    expect($guidelines)->toHaveCount(1);
});

test('boost guideline service can get guidelines for context', function () {
    $service = new BoostGuidelineService();

    $service->addGuideline([
        'context' => 'code-generation',
        'rule' => 'Use type hints',
    ]);

    $guidelines = $service->getGuidelinesForContext('code-generation');

    expect($guidelines)->toHaveCount(1)
        ->and($guidelines[0]['rule'])->toBe('Use type hints');
});

test('boost guideline service can load guidelines from file', function () {
    $service = new BoostGuidelineService();
    $testFile = storage_path('app/test-guidelines.json');

    // Create test file
    file_put_contents($testFile, json_encode([
        ['context' => 'test', 'rule' => 'Test rule'],
    ]));

    $service->loadGuidelines($testFile);

    $guidelines = $service->getGuidelines();

    expect($guidelines)->not->toBeEmpty();

    // Cleanup
    if (file_exists($testFile)) {
        unlink($testFile);
    }
});

