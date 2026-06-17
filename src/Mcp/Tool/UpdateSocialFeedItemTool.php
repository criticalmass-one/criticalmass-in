<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\SocialNetworkFeedItem;
use App\OAuth2\OAuthScope;

/**
 * Write-Tool: spiegelt POST /api/{citySlug}/socialnetwork-feeditems/{feedItemId}.
 */
final class UpdateSocialFeedItemTool extends AbstractWriteTool
{
    public function name(): string
    {
        return 'update_social_feeditem';
    }

    public function description(): string
    {
        return 'Aktualisiert einen bestehenden Social-Network-Feed-Eintrag.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'feedItemId' => ['type' => 'integer', 'description' => 'ID des Feed-Eintrags.'],
                'feedItem' => [
                    'type' => 'object',
                    'description' => 'Zu ändernde Feed-Item-Felder (api-write).',
                ],
            ],
            'required' => ['feedItemId', 'feedItem'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::SocialNetworkWrite;
    }

    public function call(array $arguments): string
    {
        $feedItem = $this->registry->getRepository(SocialNetworkFeedItem::class)->find((int) ($arguments['feedItemId'] ?? 0));

        if (!$feedItem instanceof SocialNetworkFeedItem) {
            throw new McpToolException('Feed-Eintrag nicht gefunden.');
        }

        $this->deserializeInto(\is_array($arguments['feedItem'] ?? null) ? $arguments['feedItem'] : [], $feedItem);
        $this->flush();

        return $this->serializer->serialize($feedItem, 'json', []);
    }
}
