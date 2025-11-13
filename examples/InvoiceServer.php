<?php

namespace App\Mcp\Servers;

use App\Mcp\Prompts\InvoiceManagementPrompt;
use App\Mcp\Resources\InvoiceSchemaResource;
use App\Mcp\Tools\GetInvoiceTool;
use App\Mcp\Tools\ListInvoicesTool;
use Emeq\McpLaravel\Infrastructure\Mcp\BaseServer;

/**
 * Example Invoice MCP Server
 *
 * This server demonstrates how to create an Invoice MCP server
 * that provides invoice retrieval and listing functionality.
 *
 * Usage:
 * 1. Create the tools: GetInvoiceTool and ListInvoicesTool
 * 2. Create the resource: InvoiceSchemaResource
 * 3. Create the prompt: InvoiceManagementPrompt
 * 4. Register this server in routes/ai.php
 * 5. Your AI assistant can now answer questions about invoices
 */
final class InvoiceServer extends BaseServer
{
    protected string $name = 'Invoice MCP Server';

    protected string $version = '1.0.0';

    protected string $instructions = 'You are an AI assistant for invoice management. '.
        'You can help users retrieve invoice information and list invoices with filters. '.
        'Always provide clear and accurate information. '.
        'When listing invoices, provide summaries. '.
        'When getting an invoice, provide all relevant details.';

    // Register invoice tools
    protected array $tools = [
        GetInvoiceTool::class,
        ListInvoicesTool::class,
    ];

    // Register invoice resources
    protected array $resources = [
        InvoiceSchemaResource::class,
    ];

    // Register invoice prompts
    protected array $prompts = [
        InvoiceManagementPrompt::class,
    ];
}
