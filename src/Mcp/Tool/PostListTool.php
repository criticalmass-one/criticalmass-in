<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\Post;
use App\OAuth2\OAuthScope;

/**
 * Read-Tool: spiegelt GET /api/post (gefilterte Forenbeitrags-Liste).
 */
final class PostListTool extends AbstractDataQueryTool
{
    public function name(): string
    {
        return 'list_posts';
    }

    public function description(): string
    {
        return 'Listet Forenbeiträge, gefiltert nach Stadt, Ride, Datum oder Geo-Umkreis.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'rideIdentifier' => ['type' => 'string', 'description' => 'Ride-Datum (YYYY-MM-DD) oder -Slug.'],
                ...self::dateProperties(),
                ...self::geoProperties(),
                ...self::commonListProperties(),
            ],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::PostRead;
    }

    protected function entityClass(): string
    {
        return Post::class;
    }

    protected function groups(array $arguments): array
    {
        return ['post-list'];
    }
}
