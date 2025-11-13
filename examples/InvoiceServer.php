<?php

namespace App\Mcp\Servers;

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
 * 2. Register this server in routes/ai.php
 * 3. Your AI assistant can now answer questions about invoices
 */
final class InvoiceServer extends BaseServer
{
    public function __construct()
    {
        $this->name = 'Invoice MCP Server';
        $this->version = '1.0.0';
        $this->instructions = 'You are an AI assistant for invoice management. '.
            'You can help users retrieve invoice information and list invoices with filters. '.
            'Always provide clear and accurate information. '.
            'When listing invoices, provide summaries. '.
            'When getting an invoice, provide all relevant details.';

        // Register invoice tools
        $this->tools = [
            GetInvoiceTool::class,
            ListInvoicesTool::class,
        ];
    }
}
