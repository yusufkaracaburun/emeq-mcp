<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp\Tools;

use Emeq\McpLaravel\Infrastructure\Mcp\BaseTool;
use Illuminate\Support\Facades\Cache;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

final class CacheOperationTool extends BaseTool
{
    public function getName(): string
    {
        return 'cache-operation';
    }

    public function getDescription(): string
    {
        return 'Perform cache operations (get, set, forget, flush).';
    }

    public function getInputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'operation' => [
                    'type' => 'string',
                    'description' => 'The operation to perform: get, set, forget, flush',
                    'enum' => ['get', 'set', 'forget', 'flush'],
                ],
                'key' => [
                    'type' => 'string',
                    'description' => 'Cache key (required for get, set, forget)',
                ],
                'value' => [
                    'type' => 'string',
                    'description' => 'Cache value (required for set)',
                ],
                'ttl' => [
                    'type' => 'integer',
                    'description' => 'Time to live in seconds (optional for set)',
                ],
            ],
            'required' => ['operation'],
        ];
    }

    public function handle(Request $request): Response
    {
        if (! config('emeq-mcp.tools.cache_operation.enabled', true)) {
            return Response::error('Cache operation tool is disabled.');
        }

        $arguments = $this->validateArguments($request->arguments());
        $operation = $arguments['operation'];

        try {
            $result = match ($operation) {
                'get' => $this->getCache($arguments['key'] ?? null),
                'set' => $this->setCache($arguments['key'] ?? null, $arguments['value'] ?? null, $arguments['ttl'] ?? null),
                'forget' => $this->forgetCache($arguments['key'] ?? null),
                'flush' => $this->flushCache(),
                default => throw new \InvalidArgumentException("Unknown operation: {$operation}"),
            };

            return Response::text(json_encode($result, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            return Response::error("Cache operation failed: {$e->getMessage()}");
        }
    }

    /**
     * Get a value from cache.
     *
     * @return array<string, mixed>
     */
    private function getCache(?string $key): array
    {
        if (! $key) {
            throw new \InvalidArgumentException('Key is required for get operation.');
        }

        $value = Cache::get($key);

        return [
            'operation' => 'get',
            'key' => $key,
            'value' => $value,
            'found' => $value !== null,
        ];
    }

    /**
     * Set a value in cache.
     *
     * @return array<string, mixed>
     */
    private function setCache(?string $key, ?string $value, ?int $ttl): array
    {
        if (! $key) {
            throw new \InvalidArgumentException('Key is required for set operation.');
        }

        if ($value === null) {
            throw new \InvalidArgumentException('Value is required for set operation.');
        }

        if ($ttl) {
            Cache::put($key, $value, $ttl);
        } else {
            Cache::forever($key, $value);
        }

        return [
            'operation' => 'set',
            'key' => $key,
            'success' => true,
        ];
    }

    /**
     * Forget a cache key.
     *
     * @return array<string, mixed>
     */
    private function forgetCache(?string $key): array
    {
        if (! $key) {
            throw new \InvalidArgumentException('Key is required for forget operation.');
        }

        Cache::forget($key);

        return [
            'operation' => 'forget',
            'key' => $key,
            'success' => true,
        ];
    }

    /**
     * Flush all cache.
     *
     * @return array<string, mixed>
     */
    private function flushCache(): array
    {
        Cache::flush();

        return [
            'operation' => 'flush',
            'success' => true,
            'message' => 'All cache cleared.',
        ];
    }
}
