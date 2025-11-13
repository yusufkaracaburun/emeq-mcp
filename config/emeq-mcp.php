<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Auto Register Pre-built Components
    |--------------------------------------------------------------------------
    |
    | When enabled, the package will automatically register all pre-built
    | tools, resources, and prompts. Set to false if you want to manually
    | register components.
    |
    */

    'auto_register' => env('EMEQ_MCP_AUTO_REGISTER', true),

    /*
    |--------------------------------------------------------------------------
    | Boost Integration
    |--------------------------------------------------------------------------
    |
    | Configuration for Laravel Boost integration.
    |
    */

    'boost' => [
        'enabled' => env('EMEQ_MCP_BOOST_ENABLED', false),
        'guidelines_path' => env('EMEQ_MCP_BOOST_GUIDELINES_PATH', base_path('.boost/guidelines')),
        'context_providers' => [
            // Add custom context providers here
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pre-built Tools
    |--------------------------------------------------------------------------
    |
    | Configuration for pre-built MCP tools.
    |
    */

    'tools' => [
        'database_query' => [
            'enabled' => env('EMEQ_MCP_TOOL_DATABASE_QUERY', true),
            'max_query_time' => env('EMEQ_MCP_MAX_QUERY_TIME', 30),
        ],
        'model_operation' => [
            'enabled' => env('EMEQ_MCP_TOOL_MODEL_OPERATION', true),
        ],
        'artisan_command' => [
            'enabled' => env('EMEQ_MCP_TOOL_ARTISAN_COMMAND', true),
            'allowed_commands' => [
                // Add allowed Artisan commands here
            ],
        ],
        'cache_operation' => [
            'enabled' => env('EMEQ_MCP_TOOL_CACHE_OPERATION', true),
        ],
        'queue_job' => [
            'enabled' => env('EMEQ_MCP_TOOL_QUEUE_JOB', true),
        ],
        'file_operation' => [
            'enabled' => env('EMEQ_MCP_TOOL_FILE_OPERATION', true),
            'allowed_paths' => [
                // Add allowed file paths here
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pre-built Resources
    |--------------------------------------------------------------------------
    |
    | Configuration for pre-built MCP resources.
    |
    */

    'resources' => [
        'model_schema' => [
            'enabled' => env('EMEQ_MCP_RESOURCE_MODEL_SCHEMA', true),
        ],
        'route_list' => [
            'enabled' => env('EMEQ_MCP_RESOURCE_ROUTE_LIST', true),
        ],
        'config' => [
            'enabled' => env('EMEQ_MCP_RESOURCE_CONFIG', true),
        ],
        'log' => [
            'enabled' => env('EMEQ_MCP_RESOURCE_LOG', true),
            'max_lines' => env('EMEQ_MCP_LOG_MAX_LINES', 100),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pre-built Prompts
    |--------------------------------------------------------------------------
    |
    | Configuration for pre-built MCP prompts.
    |
    */

    'prompts' => [
        'code_generation' => [
            'enabled' => env('EMEQ_MCP_PROMPT_CODE_GENERATION', true),
        ],
        'debugging' => [
            'enabled' => env('EMEQ_MCP_PROMPT_DEBUGGING', true),
        ],
        'database_design' => [
            'enabled' => env('EMEQ_MCP_PROMPT_DATABASE_DESIGN', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Server Configuration
    |--------------------------------------------------------------------------
    |
    | Default configuration for MCP servers.
    |
    */

    'server' => [
        'default_name' => env('EMEQ_MCP_SERVER_NAME', 'Laravel MCP Server'),
        'default_version' => env('EMEQ_MCP_SERVER_VERSION', '1.0.0'),
    ],
];

