<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp\Resources;

use Emeq\McpLaravel\Infrastructure\Mcp\BaseResource;
use Illuminate\Support\Facades\Route;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

final class RouteListResource extends BaseResource
{
    public function getUri(): string
    {
        return 'laravel://routes';
    }

    public function getName(): string
    {
        return 'Route List';
    }

    public function getDescription(): string
    {
        return 'Get a list of all registered routes in the Laravel application.';
    }

    public function getMimeType(): string
    {
        return 'application/json';
    }

    public function handle(Request $request): Response
    {
        if (! config('emeq-mcp.resources.route_list.enabled', true)) {
            return Response::error('Route list resource is disabled.');
        }

        try {
            $routes = $this->getRoutes();

            return Response::text(json_encode($routes, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            return Response::error("Failed to get routes: {$e->getMessage()}");
        }
    }

    /**
     * Get all registered routes.
     *
     * @return array<string, mixed>
     */
    private function getRoutes(): array
    {
        $routes = [];
        $routeCollection = Route::getRoutes();

        foreach ($routeCollection as $route) {
            $routes[] = [
                'uri' => $route->uri(),
                'methods' => $route->methods(),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
                'middleware' => $route->middleware(),
            ];
        }

        return [
            'routes' => $routes,
            'count' => count($routes),
        ];
    }
}

