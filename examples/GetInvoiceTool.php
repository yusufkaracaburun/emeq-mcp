<?php

namespace App\Mcp\Tools;

use App\Models\Invoice;
use Emeq\McpLaravel\Infrastructure\Mcp\BaseTool;
use Illuminate\Support\Facades\Log;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

/**
 * Example Get Invoice Tool
 *
 * This tool retrieves invoice details by invoice number or ID.
 * It includes invoice items, payments, and customer information.
 */
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
            Log::error('Get invoice error', [
                'error' => $e->getMessage(),
                'arguments' => $arguments,
            ]);

            return Response::error("Failed to retrieve invoice: {$e->getMessage()}");
        }
    }
}

