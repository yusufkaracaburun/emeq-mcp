<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp\Tools;

use Emeq\McpLaravel\Infrastructure\Mcp\BaseTool;
use Exception;
use Illuminate\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\DB;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

final class DatabaseQueryTool extends BaseTool
{
    public function getName(): string
    {
        return 'database-query';
    }

    public function getDescription(): string
    {
        return 'Execute a database query and return the results. Supports SELECT queries only for security.';
    }

    public function getInputSchema(JsonSchema $schema): array
    {
        return [
            'type'       => 'object',
            'properties' => [
                'query' => [
                    'type'        => 'string',
                    'description' => 'The SQL query to execute (SELECT only)',
                ],
                'bindings' => [
                    'type'        => 'array',
                    'description' => 'Query parameter bindings',
                    'items'       => [
                        'type' => 'string',
                    ],
                ],
            ],
            'required' => ['query'],
        ];
    }

    public function handle(Request $request): Response
    {
        if ( ! config('emeq-mcp.tools.database_query.enabled', true)) {
            return Response::error('Database query tool is disabled.');
        }

        $arguments = $this->validateArguments($request->arguments());
        $query     = $arguments['query'];
        $bindings  = $arguments['bindings'] ?? [];

        // Security: Only allow SELECT queries
        if ( ! preg_match('/^\s*SELECT\s+/i', trim($query))) {
            return Response::error('Only SELECT queries are allowed for security reasons.');
        }

        try {
            $maxTime   = config('emeq-mcp.tools.database_query.max_query_time', 30);
            $startTime = microtime(true);

            $results = DB::select($query, $bindings);

            $executionTime = microtime(true) - $startTime;

            if ($executionTime > $maxTime) {
                return Response::error("Query execution time exceeded maximum allowed time of {$maxTime} seconds.");
            }

            return Response::text(
                json_encode([
                    'results'        => $results,
                    'count'          => count($results),
                    'execution_time' => round($executionTime, 4),
                ], JSON_PRETTY_PRINT)
            );
        } catch (Exception $e) {
            return Response::error("Database query failed: {$e->getMessage()}");
        }
    }
}
