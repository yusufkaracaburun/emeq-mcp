<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp\Resources;

use Emeq\McpLaravel\Infrastructure\Mcp\BaseResource;
use Illuminate\Support\Facades\File;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

final class LogResource extends BaseResource
{
    public function getUri(): string
    {
        return 'laravel://logs';
    }

    public function getName(): string
    {
        return 'Application Logs';
    }

    public function getDescription(): string
    {
        return 'Get application log entries.';
    }

    public function getMimeType(): string
    {
        return 'text/plain';
    }

    public function handle(Request $request): Response
    {
        if (! config('emeq-mcp.resources.log.enabled', true)) {
            return Response::error('Log resource is disabled.');
        }

        try {
            $maxLines = config('emeq-mcp.resources.log.max_lines', 100);
            $logs = $this->getLogs($maxLines);

            return Response::text($logs);
        } catch (\Exception $e) {
            return Response::error("Failed to get logs: {$e->getMessage()}");
        }
    }

    /**
     * Get log entries.
     */
    private function getLogs(int $maxLines): string
    {
        $logPath = storage_path('logs/laravel.log');

        if (! File::exists($logPath)) {
            return 'No log file found.';
        }

        $lines = file($logPath);
        if ($lines === false) {
            return 'Failed to read log file.';
        }

        $totalLines = count($lines);
        $linesToShow = min($maxLines, $totalLines);

        $recentLines = array_slice($lines, -$linesToShow);

        return implode('', $recentLines)."\n\n---\nShowing {$linesToShow} of {$totalLines} lines";
    }
}
