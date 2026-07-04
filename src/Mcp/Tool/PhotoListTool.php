<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\Photo;
use App\OAuth2\OAuthScope;

/**
 * Read-Tool: spiegelt GET /api/photo (gefilterte Foto-Liste).
 */
final class PhotoListTool extends AbstractDataQueryTool
{
    public function name(): string
    {
        return 'list_photos';
    }

    public function description(): string
    {
        return 'Listet Fotos, gefiltert nach Stadt, Region, Ride, Datum oder Geo-Umkreis.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'regionSlug' => ['type' => 'string', 'description' => 'Slug der Region.'],
                'rideIdentifier' => ['type' => 'string', 'description' => 'Ride-Datum (YYYY-MM-DD) oder -Slug.'],
                ...self::dateProperties(),
                ...self::geoProperties(),
                ...self::commonListProperties(),
            ],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::PhotoRead;
    }

    protected function entityClass(): string
    {
        return Photo::class;
    }

    protected function groups(array $arguments): array
    {
        return [];
    }
}
