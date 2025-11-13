<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp\Resources;

use Emeq\McpLaravel\Infrastructure\Mcp\BaseResource;
use Illuminate\Support\Facades\Config;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

final class ConfigResource extends BaseResource
{
    public function getUri(): string
    {
        return 'laravel://config';
    }

    public function getName(): string
    {
        return 'Configuration';
    }

    public function getDescription(): string
    {
        return 'Get configuration values from the Laravel application.';
    }

    public function getMimeType(): string
    {
        return 'application/json';
    }

    public function handle(Request $request): Response
    {
        if (! config('emeq-mcp.resources.config.enabled', true)) {
            return Response::error('Config resource is disabled.');
        }

        $uri = $request->uri();
        $configKey = $this->extractConfigKeyFromUri($uri);

        try {
            if ($configKey) {
                $value = Config::get($configKey);
                $data = [
                    'key' => $configKey,
                    'value' => $value,
                ];
            } else {
                // Return all config (be careful with sensitive data)
                $data = [
                    'message' => 'Use specific config key in URI to get values. Example: laravel://config/app.name',
                ];
            }

            return Response::text(json_encode($data, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            return Response::error("Failed to get config: {$e->getMessage()}");
        }
    }

    /**
     * Extract config key from URI.
     */
    private function extractConfigKeyFromUri(string $uri): ?string
    {
        if (preg_match('/config\/(.+)$/', $uri, $matches)) {
            return str_replace('/', '.', $matches[1]);
        }

        return null;
    }
}

