<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp\Tools;

use Emeq\McpLaravel\Infrastructure\Mcp\BaseTool;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\JsonSchema\JsonSchema;
use InvalidArgumentException;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use RuntimeException;

final class ModelOperationTool extends BaseTool
{
    public function getName(): string
    {
        return 'model-operation';
    }

    public function getDescription(): string
    {
        return 'Perform CRUD operations on Eloquent models (create, read, update, delete).';
    }

    public function getInputSchema(JsonSchema $schema): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'model' => [
                    'type' => 'string',
                    'description' => 'The fully qualified model class name',
                ],
                'operation' => [
                    'type' => 'string',
                    'description' => 'The operation to perform: create, read, update, delete',
                    'enum' => ['create', 'read', 'update', 'delete'],
                ],
                'id' => [
                    'type' => 'integer',
                    'description' => 'Model ID for read, update, or delete operations',
                ],
                'attributes' => [
                    'type' => 'object',
                    'description' => 'Model attributes for create or update operations',
                ],
            ],
            'required' => ['model', 'operation'],
        ];
    }

    public function handle(Request $request): Response
    {
        if (! config('emeq-mcp.tools.model_operation.enabled', true)) {
            return Response::error('Model operation tool is disabled.');
        }

        $arguments = $this->validateArguments($request->all());
        $modelClass = $arguments['model'];
        $operation = $arguments['operation'];

        if (! class_exists($modelClass) || ! is_subclass_of($modelClass, Model::class)) {
            return Response::error("Invalid model class: {$modelClass}");
        }

        try {
            $result = match ($operation) {
                'create' => $this->createModel($modelClass, $arguments['attributes'] ?? []),
                'read' => $this->readModel($modelClass, $arguments['id'] ?? null),
                'update' => $this->updateModel($modelClass, $arguments['id'], $arguments['attributes'] ?? []),
                'delete' => $this->deleteModel($modelClass, $arguments['id']),
                default => throw new InvalidArgumentException("Unknown operation: {$operation}"),
            };

            return Response::text(json_encode($result, JSON_PRETTY_PRINT));
        } catch (Exception $e) {
            return Response::error("Model operation failed: {$e->getMessage()}");
        }
    }

    /**
     * Create a new model instance.
     *
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function createModel(string $modelClass, array $attributes): array
    {
        $model = new $modelClass($attributes);
        $model->save();

        return [
            'operation' => 'create',
            'success' => true,
            'model' => $model->toArray(),
        ];
    }

    /**
     * Read a model instance.
     *
     * @param  class-string<Model>  $modelClass
     * @return array<string, mixed>
     */
    private function readModel(string $modelClass, ?int $id): array
    {
        if ($id) {
            $model = $modelClass::find($id);

            if (! $model) {
                throw new RuntimeException("Model with ID {$id} not found.");
            }

            return [
                'operation' => 'read',
                'success' => true,
                'model' => $model->toArray(),
            ];
        }

        $models = $modelClass::all();

        return [
            'operation' => 'read',
            'success' => true,
            'models' => $models->toArray(),
            'count' => $models->count(),
        ];
    }

    /**
     * Update a model instance.
     *
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function updateModel(string $modelClass, ?int $id, array $attributes): array
    {
        if (! $id) {
            throw new InvalidArgumentException('ID is required for update operation.');
        }

        $model = $modelClass::find($id);

        if (! $model) {
            throw new RuntimeException("Model with ID {$id} not found.");
        }

        $model->fill($attributes);
        $model->save();

        return [
            'operation' => 'update',
            'success' => true,
            'model' => $model->toArray(),
        ];
    }

    /**
     * Delete a model instance.
     *
     * @param  class-string<Model>  $modelClass
     * @return array<string, mixed>
     */
    private function deleteModel(string $modelClass, ?int $id): array
    {
        if (! $id) {
            throw new InvalidArgumentException('ID is required for delete operation.');
        }

        $model = $modelClass::find($id);

        if (! $model) {
            throw new RuntimeException("Model with ID {$id} not found.");
        }

        $model->delete();

        return [
            'operation' => 'delete',
            'success' => true,
            'message' => "Model with ID {$id} deleted successfully.",
        ];
    }
}
