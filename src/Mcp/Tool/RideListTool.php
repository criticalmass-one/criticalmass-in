<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\Ride;
use App\OAuth2\OAuthScope;

/**
 * Read-Tool: spiegelt GET /api/ride (gefilterte Ride-Liste).
 */
final class RideListTool extends AbstractDataQueryTool
{
    public function name(): string
    {
        return 'list_rides';
    }

    public function description(): string
    {
        return 'Listet Critical-Mass-Rides, gefiltert nach Stadt, Region, Typ, Datum oder Geo-Umkreis.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt, z. B. "hamburg".'],
                'regionSlug' => ['type' => 'string', 'description' => 'Slug der Region.'],
                'rideType' => ['type' => 'string', 'description' => 'Ride-Typ (mehrere kommagetrennt).'],
                'extended' => ['type' => 'boolean', 'description' => 'Erweiterte Felder einbeziehen.'],
                ...self::dateProperties(),
                ...self::geoProperties(),
                ...self::commonListProperties(),
            ],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::RideRead;
    }

    protected function entityClass(): string
    {
        return Ride::class;
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
