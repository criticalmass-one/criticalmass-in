<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\Track;
use App\OAuth2\OAuthScope;

/**
 * Read-Tool: spiegelt GET /api/track (gefilterte Track-Liste, öffentliche Felder).
 */
final class TrackListTool extends AbstractDataQueryTool
{
    public function name(): string
    {
        return 'list_tracks';
    }

    public function description(): string
    {
        return 'Listet GPS-Tracks, gefiltert nach Stadt, Region oder Datum.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'regionSlug' => ['type' => 'string', 'description' => 'Slug der Region.'],
                ...self::dateProperties(),
                ...self::commonListProperties(),
            ],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::TrackRead;
    }

    protected function entityClass(): string
    {
        return Track::class;
    }

    protected function groups(array $arguments): array
    {
        return ['api-public'];
    }
}
