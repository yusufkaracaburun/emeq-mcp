# Emeq MCP Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/emeq/emeq-mcp-laravel.svg?style=flat-square)](https://packagist.org/packages/emeq/emeq-mcp-laravel)
[![Total Downloads](https://img.shields.io/packagist/dt/emeq/emeq-mcp-laravel.svg?style=flat-square)](https://packagist.org/packages/emeq/emeq-mcp-laravel)

Laravel package for MCP (Model Context Protocol) with Boost integration, providing helper utilities, pre-built tools/resources/prompts, and seamless integration between Laravel MCP and Boost.

## Features

- **Helper Utilities**: Fluent builders for creating MCP servers, tools, resources, and prompts
- **Pre-built Components**: Ready-to-use tools, resources, and prompts for common Laravel operations
- **Boost Integration**: Seamless integration with Laravel Boost for AI guidelines and development tools
- **Domain-Driven Design**: Clean architecture following DDD principles
- **SOLID Principles**: Well-structured, maintainable codebase
- **Type Safety**: Value objects for enhanced type safety

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

Let's create a complete Invoice MCP server with tools, resources, and prompts to get and list invoices, provide schema information, and assist with invoice management.

### Step 1: Create the Invoice Server

```bash
php artisan make:mcp-server InvoiceServer
```

This creates `app/Mcp/Servers/InvoiceServer.php`. We'll update it in Step 5 after creating the tools, resources, and prompts.

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

### Step 3: Create Invoice Resources

#### Invoice Schema Resource

```bash
php artisan make:mcp-resource InvoiceSchema
```

Edit `app/Mcp/Resources/InvoiceSchemaResource.php`:

```php
<?php

namespace App\Mcp\Resources;

use App\Models\Invoice;
use Emeq\McpLaravel\Infrastructure\Mcp\BaseResource;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

final class InvoiceSchemaResource extends BaseResource
{
    public function getUri(): string
    {
        return 'invoice://schema';
    }

    public function getName(): string
    {
        return 'Invoice Schema';
    }

    public function getDescription(): string
    {
        return 'Get the schema information for the Invoice model, including fields, relationships, and status values.';
    }

    public function getMimeType(): string
    {
        return 'application/json';
    }

    public function handle(Request $request): Response
    {
        try {
            $schema = $this->getInvoiceSchema();

            return Response::text(json_encode($schema, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            return Response::error("Failed to get invoice schema: {$e->getMessage()}");
        }
    }

    private function getInvoiceSchema(): array
    {
        $model = new Invoice();
        $table = $model->getTable();

        return [
            'model' => Invoice::class,
            'table' => $table,
            'fields' => [
                'id' => [
                    'type' => 'integer',
                    'description' => 'Primary key',
                ],
                'invoice_number' => [
                    'type' => 'string',
                    'description' => 'Invoice number (e.g., INV-2025-001)',
                ],
                'customer_id' => [
                    'type' => 'integer',
                    'description' => 'Foreign key to customers table',
                ],
                'status' => [
                    'type' => 'enum',
                    'values' => ['draft', 'sent', 'paid', 'overdue', 'cancelled'],
                    'description' => 'Invoice status',
                ],
                'issue_date' => [
                    'type' => 'date',
                    'description' => 'Invoice issue date',
                ],
                'due_date' => [
                    'type' => 'date',
                    'description' => 'Invoice due date',
                ],
                'subtotal' => [
                    'type' => 'decimal',
                    'description' => 'Subtotal amount before tax',
                ],
                'tax' => [
                    'type' => 'decimal',
                    'description' => 'Tax amount',
                ],
                'total' => [
                    'type' => 'decimal',
                    'description' => 'Total invoice amount',
                ],
                'paid_amount' => [
                    'type' => 'decimal',
                    'description' => 'Total amount paid',
                ],
                'balance' => [
                    'type' => 'decimal',
                    'description' => 'Remaining balance (total - paid_amount)',
                ],
            ],
            'relationships' => [
                'customer' => [
                    'type' => 'BelongsTo',
                    'model' => 'App\Models\Customer',
                    'description' => 'The customer associated with this invoice',
                ],
                'items' => [
                    'type' => 'HasMany',
                    'model' => 'App\Models\InvoiceItem',
                    'description' => 'Line items on the invoice',
                ],
                'payments' => [
                    'type' => 'HasMany',
                    'model' => 'App\Models\Payment',
                    'description' => 'Payments made against this invoice',
                ],
            ],
            'status_values' => [
                'draft' => 'Invoice is in draft state',
                'sent' => 'Invoice has been sent to the customer',
                'paid' => 'Invoice has been fully paid',
                'overdue' => 'Invoice is past its due date',
                'cancelled' => 'Invoice has been cancelled',
            ],
        ];
    }
}
```

### Step 4: Create Invoice Prompts

#### Invoice Management Prompt

```bash
php artisan make:mcp-prompt InvoiceManagement
```

Edit `app/Mcp/Prompts/InvoiceManagementPrompt.php`:

```php
<?php

namespace App\Mcp\Prompts;

use Emeq\McpLaravel\Domain\Mcp\ValueObjects\PromptTemplate;
use Emeq\McpLaravel\Infrastructure\Mcp\BasePrompt;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

final class InvoiceManagementPrompt extends BasePrompt
{
    public function getName(): string
    {
        return 'invoice-management';
    }

    public function getDescription(): string
    {
        return 'Get assistance with invoice management tasks, including creating, updating, and managing invoices.';
    }

    public function getArguments(): array
    {
        return [
            'task' => [
                'type' => 'string',
                'description' => 'The invoice management task (create, update, send, cancel, etc.)',
            ],
            'context' => [
                'type' => 'string',
                'description' => 'Additional context or requirements for the task',
            ],
        ];
    }

    protected function getTemplate(): PromptTemplate
    {
        return new PromptTemplate(
            "You are assisting with invoice management in a Laravel application.\n\n" .
            "Task: {{task}}\n\n" .
            "Context: {{context}}\n\n" .
            "The Invoice model has the following key features:\n" .
            "- Invoice numbers are formatted as INV-YYYY-SEQ (e.g., INV-2025-001)\n" .
            "- Status values: draft, sent, paid, overdue, cancelled\n" .
            "- Relationships: customer, items (line items), payments\n" .
            "- Financial fields: subtotal, tax, total, paid_amount, balance\n" .
            "- Dates: issue_date (when invoice was created), due_date (payment deadline)\n\n" .
            "Provide clear, accurate guidance following Laravel best practices."
        );
    }

    public function handle(Request $request): Response
    {
        $arguments = $this->validateArguments($request->arguments());
        $template = $this->getTemplate();

        $rendered = $template->render([
            'task' => $arguments['task'] ?? 'invoice management',
            'context' => $arguments['context'] ?? 'No specific context provided',
        ]);

        return Response::text($rendered);
    }
}
```

### Step 5: Update InvoiceServer to Include Resources and Prompts

Update `app/Mcp/Servers/InvoiceServer.php` to include the resource and prompt:

```php
<?php

namespace App\Mcp\Servers;

use App\Mcp\Prompts\InvoiceManagementPrompt;
use App\Mcp\Resources\InvoiceSchemaResource;
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

        $this->resources = [
            InvoiceSchemaResource::class,
        ];

        $this->prompts = [
            InvoiceManagementPrompt::class,
        ];
    }
}
```

### Step 6: Register the Server

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

### Step 7: Test Your Server

Your AI assistant can now answer questions like:

- "Show me invoice INV-2025-001"
- "List all paid invoices from this month"
- "What invoices are overdue?"
- "What is the schema for the Invoice model?" (uses InvoiceSchemaResource)
- "Help me create a new invoice" (uses InvoiceManagementPrompt)

## AI Assistant Integration

Once you've created and registered your MCP servers, you can integrate them with various AI assistants. The MCP (Model Context Protocol) allows AI assistants to access your Laravel application's tools, resources, and prompts.

### Cursor

Cursor has built-in support for MCP servers. To configure your Laravel MCP server in Cursor:

1. **Create or edit `.cursor/mcp.json`** in your project root:

```json
{
    "mcpServers": {
        "laravel-invoice": {
            "command": "php",
            "args": ["artisan", "mcp:start", "invoice"],
            "cwd": "/path/to/your/laravel/project"
        }
    }
}
```

2. **Restart Cursor** to load the MCP server configuration.

3. **Verify the connection**: Open Cursor's MCP panel to see your registered servers, tools, resources, and prompts.

**Note**: Replace `/path/to/your/laravel/project` with the absolute path to your Laravel project directory.

### Claude Desktop

Claude Desktop supports MCP servers through its configuration file:

1. **Locate Claude's configuration file**:

    - **macOS**: `~/Library/Application Support/Claude/claude_desktop_config.json`
    - **Windows**: `%APPDATA%\Claude\claude_desktop_config.json`
    - **Linux**: `~/.config/Claude/claude_desktop_config.json`

2. **Add your Laravel MCP server** to the `mcpServers` section:

```json
{
    "mcpServers": {
        "laravel-invoice": {
            "command": "php",
            "args": [
                "/absolute/path/to/your/project/artisan",
                "mcp:start",
                "invoice"
            ],
            "cwd": "/absolute/path/to/your/project"
        }
    }
}
```

3. **Restart Claude Desktop** to apply the changes.

4. **Test the connection**: Ask Claude to use your invoice tools, for example: "Get invoice FACTUUR-001 using the get-invoice tool"

### N8N

N8N can integrate with MCP servers using HTTP endpoints or by executing the MCP server as a subprocess. Here are two approaches:

#### Option 1: HTTP Endpoint (Recommended)

If you've registered your server using `Mcp::web()`, you can access it via HTTP:

1. **Register your server as a web endpoint** in `routes/ai.php`:

```php
use App\Mcp\Servers\InvoiceServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp/invoice', InvoiceServer::class);
```

2. **Create an N8N HTTP Request node** that calls your MCP endpoint:

```json
{
    "method": "POST",
    "url": "https://your-laravel-app.com/mcp/invoice",
    "headers": {
        "Content-Type": "application/json"
    },
    "body": {
        "jsonrpc": "2.0",
        "id": 1,
        "method": "tools/call",
        "params": {
            "name": "get-invoice",
            "arguments": {
                "serial_number": "FACTUUR-001"
            }
        }
    }
}
```

#### Option 2: Subprocess Execution

You can execute the MCP server as a subprocess in N8N:

1. **Create an Execute Command node** in N8N:

```bash
php /path/to/your/project/artisan mcp:start invoice
```

2. **Use the MCP protocol** to communicate with the server via stdin/stdout.

### Testing Your Integration

After configuring your AI assistant, test the integration:

1. **List available tools**: Ask your AI assistant to list available MCP tools
2. **Call a tool**: Try using one of your tools, e.g., "Get invoice FACTUUR-001"
3. **Access resources**: Request a resource, e.g., "Show me the invoice schema"
4. **Use prompts**: Ask for assistance using your prompts, e.g., "Help me create a new invoice"

### Troubleshooting

**Server not appearing in Cursor/Claude:**

- Verify the path in `mcp.json` is absolute and correct
- Ensure PHP is in your system PATH or use the full path to PHP
- Check that `artisan mcp:start invoice` works from the command line
- Review Cursor/Claude logs for error messages

**Tools not working:**

- Verify your server is registered in `routes/ai.php`
- Check that tools are properly added to the `$tools` array in your server class
- Ensure your Laravel application is running and accessible

**Connection errors:**

- Verify database connections are working
- Check Laravel logs for errors: `storage/logs/laravel.log`
- Ensure all required dependencies are installed: `composer install`

### Multiple Servers

You can register multiple MCP servers and configure them all:

```json
{
    "mcpServers": {
        "laravel-invoice": {
            "command": "php",
            "args": ["artisan", "mcp:start", "invoice"],
            "cwd": "/path/to/project"
        },
        "laravel-orders": {
            "command": "php",
            "args": ["artisan", "mcp:start", "orders"],
            "cwd": "/path/to/project"
        }
    }
}
```

Each server handle corresponds to the first argument passed to `Mcp::local()` in your `routes/ai.php` file.

## Configuration

### Configuration File

The main configuration file is located at `config/emeq-mcp.php`. Key settings include:

- `auto_register`: Automatically register pre-built components (default: `true`)
- `boost.enabled`: Enable Boost integration (default: `false`)
- `boost.guidelines_path`: Path to Boost guidelines directory (default: `.boost/guidelines`)
- `server.default_name`: Default MCP server name
- `server.default_version`: Default MCP server version

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

- **DatabaseQueryTool**: Execute SELECT queries safely
- **ModelOperationTool**: CRUD operations on Eloquent models
- **ArtisanCommandTool**: Execute Artisan commands
- **CacheOperationTool**: Cache operations (get, set, forget, flush)
- **QueueJobTool**: Dispatch jobs to queues
- **FileOperationTool**: File system operations

### Resources

- **ModelSchemaResource**: Eloquent model schemas
- **RouteListResource**: Application routes
- **ConfigResource**: Configuration values
- **LogResource**: Application logs

### Prompts

- **CodeGenerationPrompt**: Code generation assistance
- **DebuggingPrompt**: Debugging assistance
- **DatabaseDesignPrompt**: Database design assistance

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

- `make:mcp-server {name}` - Create a new MCP server
- `make:mcp-tool {name}` - Create a new MCP tool
- `make:mcp-resource {name}` - Create a new MCP resource
- `make:mcp-prompt {name}` - Create a new MCP prompt
- `mcp:boost-install` - Install Boost integration
- `mcp:list` - List all registered MCP components

## Architecture

The package follows Domain-Driven Design principles:

- **Domain Layer**: Contracts, entities, value objects, and domain services
- **Infrastructure Layer**: Base classes, builders, and pre-built components
- **Application Layer**: Commands and application services
- **Support Layer**: Facades and helper functions

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

- [Emeq](https://github.com/emeq)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
