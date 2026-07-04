<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\SocialNetworkFeedItem;
use App\Entity\SocialNetworkProfile;
use App\OAuth2\OAuthScope;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

/**
 * Write-Tool: spiegelt PUT /api/{citySlug}/socialnetwork-feeditems.
 */
final class CreateSocialFeedItemTool extends AbstractWriteTool
{
    public function name(): string
    {
        return 'create_social_feeditem';
    }

    public function description(): string
    {
        return 'Legt einen Social-Network-Feed-Eintrag für ein Profil an.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'socialNetworkProfileId' => ['type' => 'integer', 'description' => 'ID des zugehörigen Profils.'],
                'feedItem' => [
                    'type' => 'object',
                    'description' => 'Feed-Item-Felder (api-write), z. B. uniqueIdentifier, createdAt, content.',
                ],
            ],
            'required' => ['socialNetworkProfileId', 'feedItem'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::SocialNetworkWrite;
    }

    public function call(array $arguments): string
    {
        $profile = $this->registry->getRepository(SocialNetworkProfile::class)->find((int) ($arguments['socialNetworkProfileId'] ?? 0));

        if (!$profile instanceof SocialNetworkProfile) {
            throw new McpToolException('Social-Network-Profil nicht gefunden.');
        }

        /** @var SocialNetworkFeedItem $feedItem */
        $feedItem = $this->deserialize(\is_array($arguments['feedItem'] ?? null) ? $arguments['feedItem'] : [], SocialNetworkFeedItem::class);
        $feedItem
            ->setSocialNetworkProfile($profile)
            ->setCreatedAt(new \DateTime());

        try {
            $this->persist($feedItem);
            $this->flush();
        } catch (UniqueConstraintViolationException) {
            throw new McpToolException('Dieser Feed-Eintrag existiert bereits.');
        }

        return $this->serializer->serialize($feedItem, 'json', []);
    }
}
