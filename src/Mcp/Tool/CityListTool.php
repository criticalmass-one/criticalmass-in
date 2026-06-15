<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\City;
use App\OAuth2\OAuthScope;

/**
 * Read-Tool: spiegelt GET /api/city (gefilterte Städte-Liste).
 */
final class CityListTool extends AbstractDataQueryTool
{
    public function name(): string
    {
        return 'list_cities';
    }

    public function description(): string
    {
        return 'Listet Städte mit Critical-Mass-Aktivität, gefiltert nach Name, Region oder Geo-Umkreis.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string', 'description' => 'Filter nach Stadtname.'],
                'regionSlug' => ['type' => 'string', 'description' => 'Slug der Region.'],
                'extended' => ['type' => 'boolean', 'description' => 'Erweiterte Felder einbeziehen.'],
                ...self::geoProperties(),
                ...self::commonListProperties(),
            ],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::CityRead;
    }

    protected function entityClass(): string
    {
        return City::class;
    }

    protected function groups(array $arguments): array
    {
        $groups = ['ride-list'];

        if (true === ($arguments['extended'] ?? false)) {
            $groups[] = 'extended-ride-list';
        }

        return $groups;
    }
}
