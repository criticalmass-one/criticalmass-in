<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\SocialNetworkFeedItem;
use App\OAuth2\OAuthScope;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Write-Tool: löscht ein einzelnes Social-Network-Feed-Item (per ID).
 */
final class DeleteSocialFeedItemTool implements McpToolInterface
{
    public function __construct(
        private readonly ManagerRegistry $registry,
    ) {
    }

    public function name(): string
    {
        return 'delete_social_feed_item';
    }

    public function description(): string
    {
        return 'Löscht ein Social-Network-Feed-Item (per ID).';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'feedItemId' => ['type' => 'integer', 'description' => 'ID des Feed-Items.'],
            ],
            'required' => ['feedItemId'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::SocialNetworkWrite;
    }

    public function call(array $arguments): string
    {
        if (!isset($arguments['feedItemId']) || !is_numeric($arguments['feedItemId'])) {
            throw new McpToolException('feedItemId ist erforderlich und muss eine Zahl sein.');
        }

        $manager = $this->registry->getManager();
        $feedItem = $manager->getRepository(SocialNetworkFeedItem::class)->find((int) $arguments['feedItemId']);

        if (null === $feedItem) {
            throw new McpToolException(sprintf('Kein Feed-Item mit der ID %d gefunden.', (int) $arguments['feedItemId']));
        }

        $id = $feedItem->getId();

        $manager->remove($feedItem);
        $manager->flush();

        return json_encode(['status' => 'ok', 'deletedFeedItemId' => $id], JSON_THROW_ON_ERROR);
    }
}
