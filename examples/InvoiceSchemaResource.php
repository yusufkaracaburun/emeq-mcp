<?php

namespace App\Mcp\Resources;

use App\Models\Invoice;
use Emeq\McpLaravel\Infrastructure\Mcp\BaseResource;
use Exception;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

/**
 * Example Invoice Schema Resource
 *
 * This resource provides schema information for the Invoice model,
 * including fields, relationships, and status values.
 */
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
        } catch (Exception $e) {
            return Response::error("Failed to get invoice schema: {$e->getMessage()}");
        }
    }

    /**
     * Get schema for the Invoice model.
     *
     * @return array<string, mixed>
     */
    private function getInvoiceSchema(): array
    {
        $model = new Invoice();
        $table = $model->getTable();

        return [
            'model'  => Invoice::class,
            'table'  => $table,
            'fields' => [
                'id' => [
                    'type'        => 'integer',
                    'description' => 'Primary key',
                ],
                'invoice_number' => [
                    'type'        => 'string',
                    'description' => 'Invoice number (e.g., INV-2025-001)',
                ],
                'customer_id' => [
                    'type'        => 'integer',
                    'description' => 'Foreign key to customers table',
                ],
                'status' => [
                    'type'        => 'enum',
                    'values'      => ['draft', 'sent', 'paid', 'overdue', 'cancelled'],
                    'description' => 'Invoice status',
                ],
                'issue_date' => [
                    'type'        => 'date',
                    'description' => 'Invoice issue date',
                ],
                'due_date' => [
                    'type'        => 'date',
                    'description' => 'Invoice due date',
                ],
                'subtotal' => [
                    'type'        => 'decimal',
                    'description' => 'Subtotal amount before tax',
                ],
                'tax' => [
                    'type'        => 'decimal',
                    'description' => 'Tax amount',
                ],
                'total' => [
                    'type'        => 'decimal',
                    'description' => 'Total invoice amount',
                ],
                'paid_amount' => [
                    'type'        => 'decimal',
                    'description' => 'Total amount paid',
                ],
                'balance' => [
                    'type'        => 'decimal',
                    'description' => 'Remaining balance (total - paid_amount)',
                ],
            ],
            'relationships' => [
                'customer' => [
                    'type'        => 'BelongsTo',
                    'model'       => 'App\Models\Customer',
                    'description' => 'The customer associated with this invoice',
                ],
                'items' => [
                    'type'        => 'HasMany',
                    'model'       => 'App\Models\InvoiceItem',
                    'description' => 'Line items on the invoice',
                ],
                'payments' => [
                    'type'        => 'HasMany',
                    'model'       => 'App\Models\Payment',
                    'description' => 'Payments made against this invoice',
                ],
            ],
            'status_values' => [
                'draft'     => 'Invoice is in draft state',
                'sent'      => 'Invoice has been sent to the customer',
                'paid'      => 'Invoice has been fully paid',
                'overdue'   => 'Invoice is past its due date',
                'cancelled' => 'Invoice has been cancelled',
            ],
        ];
    }
}
