<?php

namespace Emeq\McpLaravel\Support\Facades;

use Emeq\McpLaravel\Application\Services\BoostIntegrationService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array getGuidelines()
 * @method static array getGuidelinesForContext(string $context)
 * @method static void addContext(array $context)
 * @method static void initialize()
 *
 * @see \Emeq\McpLaravel\Application\Services\BoostIntegrationService
 */
class Boost extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BoostIntegrationService::class;
    }
}
