<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp\Resources;

use Emeq\McpLaravel\Infrastructure\Mcp\BaseResource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

final class ModelSchemaResource extends BaseResource
{
    public function getUri(): string
    {
        return 'laravel://model-schema';
    }

    public function getName(): string
    {
        return 'Model Schema';
    }

    public function getDescription(): string
    {
        return 'Get the schema information for Eloquent models in the application.';
    }

    public function getMimeType(): string
    {
        return 'application/json';
    }

    public function handle(Request $request): Response
    {
        if (! config('emeq-mcp.resources.model_schema.enabled', true)) {
            return Response::error('Model schema resource is disabled.');
        }

        $uri = $request->uri();
        $modelName = $this->extractModelNameFromUri($uri);

        try {
            if ($modelName) {
                $schema = $this->getModelSchema($modelName);
            } else {
                $schema = $this->getAllModelsSchema();
            }

            return Response::text(json_encode($schema, JSON_PRETTY_PRINT));
        } catch (\Exception $e) {
            return Response::error("Failed to get model schema: {$e->getMessage()}");
        }
    }

    /**
     * Extract model name from URI.
     */
    private function extractModelNameFromUri(string $uri): ?string
    {
        if (preg_match('/model-schema\/(.+)$/', $uri, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Get schema for a specific model.
     *
     * @return array<string, mixed>
     */
    private function getModelSchema(string $modelName): array
    {
        $modelClass = $this->resolveModelClass($modelName);

        if (! $modelClass || ! class_exists($modelClass)) {
            throw new \RuntimeException("Model class not found: {$modelName}");
        }

        $model = new $modelClass;
        $table = $model->getTable();
        $fillable = $model->getFillable();
        $hidden = $model->getHidden();
        $casts = $model->getCasts();
        $dates = $model->getDates();

        return [
            'model' => $modelClass,
            'table' => $table,
            'fillable' => $fillable,
            'hidden' => $hidden,
            'casts' => $casts,
            'dates' => $dates,
            'relationships' => $this->getModelRelationships($model),
        ];
    }

    /**
     * Get schema for all models.
     *
     * @return array<string, mixed>
     */
    private function getAllModelsSchema(): array
    {
        $models = $this->discoverModels();
        $schemas = [];

        foreach ($models as $modelClass) {
            try {
                $model = new $modelClass;
                $schemas[$modelClass] = [
                    'table' => $model->getTable(),
                    'fillable' => $model->getFillable(),
                ];
            } catch (\Exception $e) {
                // Skip models that can't be instantiated
                continue;
            }
        }

        return [
            'models' => $schemas,
            'count' => count($schemas),
        ];
    }

    /**
     * Discover all Eloquent models in the application.
     *
     * @return array<int, class-string>
     */
    private function discoverModels(): array
    {
        $models = [];
        $appPath = app_path('Models');

        if (! is_dir($appPath)) {
            return $models;
        }

        $files = glob($appPath.'/*.php');

        foreach ($files as $file) {
            $className = 'App\\Models\\'.basename($file, '.php');
            if (class_exists($className) && is_subclass_of($className, Model::class)) {
                $models[] = $className;
            }
        }

        return $models;
    }

    /**
     * Resolve model class name.
     *
     * @return class-string|null
     */
    private function resolveModelClass(string $modelName): ?string
    {
        // Try App\Models\{ModelName}
        $class = 'App\\Models\\'.Str::studly($modelName);
        if (class_exists($class)) {
            return $class;
        }

        // Try direct class name
        if (class_exists($modelName)) {
            return $modelName;
        }

        return null;
    }

    /**
     * Get model relationships.
     *
     * @param  Model  $model
     * @return array<string, mixed>
     */
    private function getModelRelationships(Model $model): array
    {
        $relationships = [];
        $reflection = new \ReflectionClass($model);

        foreach ($reflection->getMethods() as $method) {
            $returnType = $method->getReturnType();
            if ($returnType && method_exists($returnType, 'getName')) {
                $returnTypeName = $returnType->getName();
                if (str_contains($returnTypeName, 'Relation') || str_contains($returnTypeName, 'BelongsTo') || str_contains($returnTypeName, 'HasMany')) {
                    $relationships[$method->getName()] = $returnTypeName;
                }
            }
        }

        return $relationships;
    }
}

