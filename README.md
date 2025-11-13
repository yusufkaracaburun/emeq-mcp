# Emeq MCP Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/emeq/emeq-mcp-laravel.svg?style=flat-square)](https://packagist.org/packages/emeq/emeq-mcp-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/emeq/emeq-mcp-laravel.svg?style=flat-square)](https://packagist.org/packages/emeq/emeq-mcp-laravel)

Laravel package for MCP (Model Context Protocol) with Boost integration, providing helper utilities, pre-built tools/resources/prompts, and seamless integration between Laravel MCP and Boost.

## Features

-   **Helper Utilities**: Fluent builders for creating MCP servers, tools, resources, and prompts
-   **Pre-built Components**: Ready-to-use tools, resources, and prompts for common Laravel operations
-   **Boost Integration**: Seamless integration with Laravel Boost for AI guidelines and development tools
-   **Domain-Driven Design**: Clean architecture following DDD principles
-   **SOLID Principles**: Well-structured, maintainable codebase
-   **Type Safety**: Value objects for enhanced type safety

## Installation

### Step 1: Install via Composer

```bash
composer require emeq/emeq-mcp-laravel
```

### Step 2: Publish Configuration

```bash
php artisan vendor:publish --tag="emeq-mcp-config"
```

This creates `config/emeq-mcp.php` where you can customize the package's behavior.

### Step 3: Publish Routes

```bash
php artisan vendor:publish --tag="emeq-mcp-routes"
```

This creates `routes/ai.php` where you can register your MCP servers.

## Quick Start: Creating an Invoice Server

Let's create a complete Invoice MCP server with tools to get and list invoices.

### Step 1: Create the Invoice Server

```bash
php artisan make:mcp-server InvoiceServer
```

This creates `app/Mcp/Servers/InvoiceServer.php`. Edit it:

```php
<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\GetInvoiceTool;
use App\Mcp\Tools\ListInvoicesTool;
use Emeq\McpLaravel\Infrastructure\Mcp\BaseServer;

final class InvoiceServer extends BaseServer
{
    public function __construct()
    {
        $this->name = 'Invoice MCP Server';
        $this->version = '1.0.0';
        $this->instructions = 'You are an AI assistant for invoice management. ' .
            'You can help users retrieve invoice information and list invoices with filters. ' .
            'Always provide clear and accurate information.';

        $this->tools = [
            GetInvoiceTool::class,
            ListInvoicesTool::class,
        ];
    }
}
```

### Step 2: Create Invoice Tools

#### Get Invoice Tool

```bash
php artisan make:mcp-tool GetInvoice
```

Edit `app/Mcp/Tools/GetInvoiceTool.php`:

```php
<?php

namespace App\Mcp\Tools;

use App\Models\Invoice;
use Emeq\McpLaravel\Infrastructure\Mcp\BaseTool;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

final class GetInvoiceTool extends BaseTool
{
    public function getName(): string
    {
        return 'get-invoice';
    }

    public function getDescription(): string
    {
        return 'Get invoice details by invoice number or ID. Returns invoice information including items, totals, status, and payment information.';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'invoice_number' => [
                    'type' => 'string',
                    'description' => 'The invoice number (e.g., INV-2025-001)',
                ],
                'invoice_id' => [
                    'type' => 'integer',
                    'description' => 'The invoice ID (alternative to invoice_number)',
                ],
            ],
            'required' => [],
        ];
    }

    public function handle(Request $request): Response
    {
        $arguments = $this->validateArguments($request->arguments());

        try {
            $query = Invoice::query();

            if (isset($arguments['invoice_number'])) {
                $invoice = $query->where('invoice_number', $arguments['invoice_number'])->first();
            } elseif (isset($arguments['invoice_id'])) {
                $invoice = $query->find($arguments['invoice_id']);
            } else {
                return Response::error('Either invoice_number or invoice_id must be provided.');
            }

            if (!$invoice) {
                return Response::error('Invoice not found. Please verify the invoice number or ID.');
            }

            // Load relationships
            $invoice->load(['items', 'payments', 'customer']);

            $result = [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'customer' => [
                    'id' => $invoice->customer->id,
                    'name' => $invoice->customer->name,
                    'email' => $invoice->customer->email,
                ],
                'status' => $invoice->status,
                'issue_date' => $invoice->issue_date?->toIso8601String(),
                'due_date' => $invoice->due_date?->toIso8601String(),
                'subtotal' => $invoice->subtotal,
                'tax' => $invoice->tax,
                'total' => $invoice->total,
                'paid_amount' => $invoice->paid_amount,
                'balance' => $invoice->balance,
                'items' => $invoice->items->map(function ($item) {
                    return [
                        'description' => $item->description,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'total' => $item->total,
                    ];
                })->toArray(),
                'payments' => $invoice->payments->map(function ($payment) {
                    return [
                        'amount' => $payment->amount,
                        'payment_date' => $payment->payment_date?->toIso8601String(),
                        'method' => $payment->method,
                    ];
                })->toArray(),
            ];

            return Response::text(json_encode($result, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            \Log::error('Get invoice error', [
                'error' => $e->getMessage(),
                'arguments' => $arguments,
            ]);

            return Response::error("Failed to retrieve invoice: {$e->getMessage()}");
        }
    }
}
```

#### List Invoices Tool

```bash
php artisan make:mcp-tool ListInvoices
```

Edit `app/Mcp/Tools/ListInvoicesTool.php`:

```php
<?php

namespace App\Mcp\Tools;

use App\Models\Invoice;
use Emeq\McpLaravel\Infrastructure\Mcp\BaseTool;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

final class ListInvoicesTool extends BaseTool
{
    public function getName(): string
    {
        return 'list-invoices';
    }

    public function getDescription(): string
    {
        return 'List invoices with optional filtering by status, date range, or customer. Returns a paginated list of invoices.';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'status' => [
                    'type' => 'string',
                    'enum' => ['draft', 'sent', 'paid', 'overdue', 'cancelled'],
                    'description' => 'Filter by invoice status',
                ],
                'customer_id' => [
                    'type' => 'integer',
                    'description' => 'Filter by customer ID',
                ],
                'date_from' => [
                    'type' => 'string',
                    'format' => 'date',
                    'description' => 'Filter invoices from this date (YYYY-MM-DD)',
                ],
                'date_to' => [
                    'type' => 'string',
                    'format' => 'date',
                    'description' => 'Filter invoices to this date (YYYY-MM-DD)',
                ],
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Maximum number of invoices to return',
                    'minimum' => 1,
                    'maximum' => 100,
                    'default' => 20,
                ],
                'page' => [
                    'type' => 'integer',
                    'description' => 'Page number for pagination',
                    'minimum' => 1,
                    'default' => 1,
                ],
            ],
            'required' => [],
        ];
    }

    public function handle(Request $request): Response
    {
        $arguments = $this->validateArguments($request->arguments());

        try {
            $query = Invoice::query();

            // Apply filters
            if (isset($arguments['status'])) {
                $query->where('status', $arguments['status']);
            }

            if (isset($arguments['customer_id'])) {
                $query->where('customer_id', $arguments['customer_id']);
            }

            if (isset($arguments['date_from'])) {
                $query->whereDate('issue_date', '>=', $arguments['date_from']);
            }

            if (isset($arguments['date_to'])) {
                $query->whereDate('issue_date', '<=', $arguments['date_to']);
            }

            // Pagination
            $limit = $arguments['limit'] ?? 20;
            $page = $arguments['page'] ?? 1;

            $invoices = $query->with('customer')
                ->orderBy('issue_date', 'desc')
                ->paginate($limit, ['*'], 'page', $page);

            $result = [
                'total' => $invoices->total(),
                'per_page' => $invoices->perPage(),
                'current_page' => $invoices->currentPage(),
                'last_page' => $invoices->lastPage(),
                'invoices' => $invoices->items()->map(function ($invoice) {
                    return [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'customer' => $invoice->customer->name,
                        'status' => $invoice->status,
                        'issue_date' => $invoice->issue_date?->toDateString(),
                        'due_date' => $invoice->due_date?->toDateString(),
                        'total' => $invoice->total,
                        'balance' => $invoice->balance,
                    ];
                })->toArray(),
            ];

            return Response::text(json_encode($result, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            \Log::error('List invoices error', [
                'error' => $e->getMessage(),
                'arguments' => $arguments,
            ]);

            return Response::error("Failed to list invoices: {$e->getMessage()}");
        }
    }
}
```

### Step 3: Register the Server

In `routes/ai.php`:

```php
<?php

use App\Mcp\Servers\InvoiceServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::server(InvoiceServer::class);
```

Or register it as a web endpoint:

```php
Mcp::web('/mcp/invoice', InvoiceServer::class);
```

### Step 4: Test Your Server

Your AI assistant can now answer questions like:

-   "Show me invoice INV-2025-001"
-   "List all paid invoices from this month"
-   "What invoices are overdue?"

## Configuration

### Configuration File

The main configuration file is located at `config/emeq-mcp.php`. Key settings include:

-   `auto_register`: Automatically register pre-built components (default: `true`)
-   `boost.enabled`: Enable Boost integration (default: `false`)
-   `boost.guidelines_path`: Path to Boost guidelines directory (default: `.boost/guidelines`)
-   `server.default_name`: Default MCP server name
-   `server.default_version`: Default MCP server version

### Environment Variables

All configuration options can be overridden using environment variables:

```env
# General
EMEQ_MCP_AUTO_REGISTER=true

# Boost Integration
EMEQ_MCP_BOOST_ENABLED=false
EMEQ_MCP_BOOST_GUIDELINES_PATH=.boost/guidelines

# MCP Server Configuration
EMEQ_MCP_SERVER_NAME="Laravel MCP Server"
EMEQ_MCP_SERVER_VERSION="1.0.0"

# Pre-built Tools Configuration
EMEQ_MCP_TOOL_DATABASE_QUERY=true
EMEQ_MCP_MAX_QUERY_TIME=30
EMEQ_MCP_TOOL_MODEL_OPERATION=true
EMEQ_MCP_TOOL_ARTISAN_COMMAND=true
EMEQ_MCP_TOOL_CACHE_OPERATION=true
EMEQ_MCP_TOOL_QUEUE_JOB=true
EMEQ_MCP_TOOL_FILE_OPERATION=true

# Pre-built Resources Configuration
EMEQ_MCP_RESOURCE_MODEL_SCHEMA=true
EMEQ_MCP_RESOURCE_ROUTE_LIST=true
EMEQ_MCP_RESOURCE_CONFIG=true
EMEQ_MCP_RESOURCE_LOG=true
EMEQ_MCP_LOG_MAX_LINES=100

# Pre-built Prompts Configuration
EMEQ_MCP_PROMPT_CODE_GENERATION=true
EMEQ_MCP_PROMPT_DEBUGGING=true
EMEQ_MCP_PROMPT_DATABASE_DESIGN=true
```

## Pre-built Components

### Tools

-   **DatabaseQueryTool**: Execute SELECT queries safely
-   **ModelOperationTool**: CRUD operations on Eloquent models
-   **ArtisanCommandTool**: Execute Artisan commands
-   **CacheOperationTool**: Cache operations (get, set, forget, flush)
-   **QueueJobTool**: Dispatch jobs to queues
-   **FileOperationTool**: File system operations

### Resources

-   **ModelSchemaResource**: Eloquent model schemas
-   **RouteListResource**: Application routes
-   **ConfigResource**: Configuration values
-   **LogResource**: Application logs

### Prompts

-   **CodeGenerationPrompt**: Code generation assistance
-   **DebuggingPrompt**: Debugging assistance
-   **DatabaseDesignPrompt**: Database design assistance

## Creating Custom Components

### Using Artisan Commands

```bash
# Create a server
php artisan make:mcp-server YourServerName

# Create a tool
php artisan make:mcp-tool YourToolName

# Create a resource
php artisan make:mcp-resource YourResourceName

# Create a prompt
php artisan make:mcp-prompt YourPromptName
```

### Using Fluent Builders

```php
use Emeq\McpLaravel\Support\Facades\Mcp;

// Create a server
$server = Mcp::server()
    ->name('My Server')
    ->version('1.0.0')
    ->instructions('Server instructions')
    ->withTool(YourTool::class)
    ->build();

// Create a tool
$tool = Mcp::tool()
    ->name('my-tool')
    ->description('Tool description')
    ->inputSchema([...])
    ->build();
```

## Boost Integration

Install Boost integration:

```bash
php artisan mcp:boost-install
```

Use Boost guidelines:

```php
use Emeq\McpLaravel\Support\Facades\Boost;

$guidelines = Boost::getGuidelines();
$contextGuidelines = Boost::getGuidelinesForContext('code-generation');
```

## Commands

-   `make:mcp-server {name}` - Create a new MCP server
-   `make:mcp-tool {name}` - Create a new MCP tool
-   `make:mcp-resource {name}` - Create a new MCP resource
-   `make:mcp-prompt {name}` - Create a new MCP prompt
-   `mcp:boost-install` - Install Boost integration
-   `mcp:list` - List all registered MCP components

## Architecture

The package follows Domain-Driven Design principles:

-   **Domain Layer**: Contracts, entities, value objects, and domain services
-   **Infrastructure Layer**: Base classes, builders, and pre-built components
-   **Application Layer**: Commands and application services
-   **Support Layer**: Facades and helper functions

## Testing

Run the test suite:

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Emeq](https://github.com/emeq)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
