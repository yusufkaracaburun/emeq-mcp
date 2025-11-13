<?php

namespace Emeq\McpLaravel\Infrastructure\Mcp\Tools;

use Emeq\McpLaravel\Infrastructure\Mcp\BaseTool;
use Exception;
use Illuminate\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Artisan;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;

final class ArtisanCommandTool extends BaseTool
{
    public function getName(): string
    {
        return 'artisan-command';
    }

    public function getDescription(): string
    {
        return 'Execute Artisan commands safely. Only allowed commands can be executed.';
    }

    public function getInputSchema(JsonSchema $schema): array
    {
        return [
            'type'       => 'object',
            'properties' => [
                'command' => [
                    'type'        => 'string',
                    'description' => 'The Artisan command to execute',
                ],
                'arguments' => [
                    'type'        => 'object',
                    'description' => 'Command arguments',
                ],
                'options' => [
                    'type'        => 'object',
                    'description' => 'Command options',
                ],
            ],
            'required' => ['command'],
        ];
    }

    public function handle(Request $request): Response
    {
        if ( ! config('emeq-mcp.tools.artisan_command.enabled', true)) {
            return Response::error('Artisan command tool is disabled.');
        }

        $arguments      = $this->validateArguments($request->all());
        $command        = $arguments['command'];
        $commandArgs    = $arguments['arguments'] ?? [];
        $commandOptions = $arguments['options'] ?? [];

        // Check if command is allowed
        $allowedCommands = config('emeq-mcp.tools.artisan_command.allowed_commands', []);

        if ( ! empty($allowedCommands) && ! in_array($command, $allowedCommands, true)) {
            return Response::error("Command '{$command}' is not in the allowed list.");
        }

        try {
            $exitCode = Artisan::call($command, array_merge($commandArgs, $commandOptions));
            $output   = Artisan::output();

            return Response::text(json_encode([
                'command'   => $command,
                'exit_code' => $exitCode,
                'output'    => $output,
                'success'   => 0 === $exitCode,
            ], JSON_PRETTY_PRINT));
        } catch (Exception $e) {
            return Response::error("Artisan command failed: {$e->getMessage()}");
        }
    }
}
