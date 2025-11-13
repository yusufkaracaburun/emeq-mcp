<?php

namespace App\Mcp\Tools;

use App\Models\Invoice;
use Emeq\McpLaravel\Infrastructure\Mcp\BaseTool;
use Illuminate\Support\Facades\Log;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

/**
 * Example List Invoices Tool
 *
 * This tool lists invoices with optional filtering by status, date range, or customer.
 * Returns a paginated list of invoices.
 */
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
            Log::error('List invoices error', [
                'error' => $e->getMessage(),
                'arguments' => $arguments,
            ]);

            return Response::error("Failed to list invoices: {$e->getMessage()}");
        }
    }
}

