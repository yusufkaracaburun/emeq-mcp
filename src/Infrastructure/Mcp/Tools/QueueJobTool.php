<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp\Tools;

use Emeq\McpLaravel\Infrastructure\Mcp\BaseTool;
use Exception;
use Illuminate\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Queue;
use InvalidArgumentException;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

final class QueueJobTool extends BaseTool
{
    public function getName(): string
    {
        return 'queue-job';
    }

    public function getDescription(): string
    {
        return 'Dispatch jobs to the queue or check queue status.';
    }

    public function getInputSchema(JsonSchema $schema): array
    {
        return [
            'type'       => 'object',
            'properties' => [
                'operation' => [
                    'type'        => 'string',
                    'description' => 'The operation to perform: dispatch, status',
                    'enum'        => ['dispatch', 'status'],
                ],
                'job_class' => [
                    'type'        => 'string',
                    'description' => 'The job class name (required for dispatch)',
                ],
                'data' => [
                    'type'        => 'object',
                    'description' => 'Job data (optional for dispatch)',
                ],
                'queue' => [
                    'type'        => 'string',
                    'description' => 'Queue name (optional)',
                ],
            ],
            'required' => ['operation'],
        ];
    }

    public function handle(Request $request): Response
    {
        if ( ! config('emeq-mcp.tools.queue_job.enabled', true)) {
            return Response::error('Queue job tool is disabled.');
        }

        $arguments = $this->validateArguments($request->all());
        $operation = $arguments['operation'];

        try {
            $result = match ($operation) {
                'dispatch' => $this->dispatchJob($arguments),
                'status'   => $this->getQueueStatus(),
                default    => throw new InvalidArgumentException("Unknown operation: {$operation}"),
            };

            return Response::text(json_encode($result, JSON_PRETTY_PRINT));
        } catch (Exception $e) {
            return Response::error("Queue operation failed: {$e->getMessage()}");
        }
    }

    /**
     * Dispatch a job to the queue.
     *
     * @param  array<string, mixed>  $arguments
     * @return array<string, mixed>
     */
    private function dispatchJob(array $arguments): array
    {
        $jobClass = $arguments['job_class'] ?? null;

        if ( ! $jobClass || ! class_exists($jobClass)) {
            throw new InvalidArgumentException("Invalid job class: {$jobClass}");
        }

        $data  = $arguments['data'] ?? [];
        $queue = $arguments['queue'] ?? null;

        $job = new $jobClass($data);

        if ($queue) {
            dispatch($job)->onQueue($queue);
        } else {
            dispatch($job);
        }

        return [
            'operation' => 'dispatch',
            'job_class' => $jobClass,
            'success'   => true,
            'message'   => 'Job dispatched successfully.',
        ];
    }

    /**
     * Get queue status.
     *
     * @return array<string, mixed>
     */
    private function getQueueStatus(): array
    {
        $connection = config('queue.default');

        return [
            'operation'  => 'status',
            'connection' => $connection,
            'driver'     => config("queue.connections.{$connection}.driver"),
        ];
    }
}
