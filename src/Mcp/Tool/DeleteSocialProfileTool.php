<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\SocialNetworkFeedItem;
use App\Entity\SocialNetworkProfile;
use App\OAuth2\OAuthScope;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Write-Tool: löscht ein Social-Network-Profil (per ID) samt seiner Feed-Items
 * (deren FK auf das Profil ist NOT NULL, daher werden sie zuerst entfernt).
 */
final class DeleteSocialProfileTool implements McpToolInterface
{
    public function __construct(
        private readonly ManagerRegistry $registry,
    ) {
    }

    public function name(): string
    {
        return 'delete_social_profile';
    }

    public function description(): string
    {
        return 'Löscht ein Social-Network-Profil (per ID) samt seiner Feed-Items.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'profileId' => ['type' => 'integer', 'description' => 'ID des Social-Network-Profils.'],
            ],
            'required' => ['profileId'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::SocialNetworkWrite;
    }

    public function call(array $arguments): string
    {
        if (!isset($arguments['profileId']) || !is_numeric($arguments['profileId'])) {
            throw new McpToolException('profileId ist erforderlich und muss eine Zahl sein.');
        }

        $manager = $this->registry->getManager();
        $profile = $manager->getRepository(SocialNetworkProfile::class)->find((int) $arguments['profileId']);

        if (null === $profile) {
            throw new McpToolException(sprintf('Kein Social-Network-Profil mit der ID %d gefunden.', (int) $arguments['profileId']));
        }

        $id = $profile->getId();

        $feedItems = $manager->getRepository(SocialNetworkFeedItem::class)->findBy(['socialNetworkProfile' => $profile]);
        foreach ($feedItems as $feedItem) {
            $manager->remove($feedItem);
        }
        $manager->flush();

        $manager->remove($profile);
        $manager->flush();

        return json_encode(['status' => 'ok', 'deletedProfileId' => $id, 'deletedFeedItems' => \count($feedItems)], JSON_THROW_ON_ERROR);
    }
}
