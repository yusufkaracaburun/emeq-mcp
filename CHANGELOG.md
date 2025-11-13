# Changelog

All notable changes to `emeq-mcp-laravel` will be documented in this file.

## [1.0.0] - 2024-01-XX

### Added
- Initial release of Emeq MCP Laravel package
- Helper utilities and abstractions for creating MCP servers, tools, resources, and prompts
- Fluent builder classes for easy component creation
- Pre-built MCP tools:
  - DatabaseQueryTool - Execute SELECT queries safely
  - ModelOperationTool - CRUD operations on Eloquent models
  - ArtisanCommandTool - Execute Artisan commands
  - CacheOperationTool - Cache operations (get, set, forget, flush)
  - QueueJobTool - Dispatch jobs to queues
  - FileOperationTool - File system operations
- Pre-built MCP resources:
  - ModelSchemaResource - Eloquent model schemas
  - RouteListResource - Application routes
  - ConfigResource - Configuration values
  - LogResource - Application logs
- Pre-built MCP prompts:
  - CodeGenerationPrompt - Code generation assistance
  - DebuggingPrompt - Debugging assistance
  - DatabaseDesignPrompt - Database design assistance
- Laravel Boost integration:
  - BoostGuidelineManager - Manages AI guidelines
  - BoostContextProvider - Provides context to Boost
  - BoostIntegrationService - Application-level Boost service
- Artisan commands:
  - `make:mcp-server` - Generate MCP server
  - `make:mcp-tool` - Generate MCP tool
  - `make:mcp-resource` - Generate MCP resource
  - `make:mcp-prompt` - Generate MCP prompt
  - `mcp:boost-install` - Install Boost integration
  - `mcp:list` - List registered MCP components
- Facades: `Mcp` and `Boost`
- Helper functions for convenient access
- Comprehensive configuration file
- Domain-Driven Design architecture following SOLID principles
- Value objects for type safety (ToolSchema, ResourceUri, PromptTemplate)
- Base classes with common functionality (DRY principle)

### Architecture
- Domain layer with contracts, entities, value objects, and services
- Infrastructure layer with base classes, builders, and pre-built components
- Application layer with commands and services
- Support layer with facades and helpers
