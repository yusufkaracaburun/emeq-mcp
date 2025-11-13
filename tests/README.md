# Test Suite

This directory contains comprehensive tests for the Emeq MCP Laravel package.

## Test Structure

### Unit Tests (`tests/Unit/`)
- **Domain/ValueObjects/**: Tests for value objects (ToolSchema, ResourceUri, PromptTemplate)
- **Domain/Entities/**: Tests for domain entities (McpServer, McpTool, McpResource, McpPrompt)
- **Domain/Boost/Services/**: Tests for Boost domain services

### Integration Tests (`tests/Integration/`)
- **Infrastructure/Builders/**: Tests for builder classes
- **Application/Services/**: Tests for application services

### Feature Tests (`tests/Feature/`)
- **Tools/**: Tests for pre-built MCP tools
- **Resources/**: Tests for pre-built MCP resources
- **Prompts/**: Tests for pre-built MCP prompts
- **Commands/**: Tests for Artisan commands

## Running Tests

```bash
# Run all tests
composer test

# Run with coverage
composer test-coverage

# Run specific test file
vendor/bin/pest tests/Unit/Domain/ValueObjects/ToolSchemaTest.php
```

## Test Coverage

The test suite covers:
- ✅ All value objects with validation tests
- ✅ All domain entities with business logic tests
- ✅ Builder classes with fluent API tests
- ✅ Application services with integration tests
- ✅ Pre-built tools with feature tests
- ✅ Pre-built resources with feature tests
- ✅ Pre-built prompts with feature tests
- ✅ Artisan commands with feature tests
- ✅ Boost integration services

## Writing New Tests

When adding new features, follow these patterns:

1. **Unit Tests**: Test individual classes in isolation
2. **Integration Tests**: Test interactions between components
3. **Feature Tests**: Test complete workflows and user-facing features

Use Pest PHP syntax for all tests, as it provides a clean and readable testing experience.

