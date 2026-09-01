<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\SocialNetworkProfile;
use App\OAuth2\OAuthScope;

/**
 * Write-Tool: spiegelt POST /api/{citySlug}/socialnetwork-profiles/{id}.
 */
final class UpdateSocialProfileTool extends AbstractWriteTool
{
    public function name(): string
    {
        return 'update_social_profile';
    }

    public function description(): string
    {
        return 'Aktualisiert ein bestehendes Social-Network-Profil.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'profileId' => ['type' => 'integer', 'description' => 'ID des Profils.'],
                'profile' => [
                    'type' => 'object',
                    'description' => 'Zu ändernde Profil-Felder (api-write).',
                ],
            ],
            'required' => ['profileId', 'profile'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::SocialNetworkWrite;
    }

    public function call(array $arguments): string
    {
        $profile = $this->registry->getRepository(SocialNetworkProfile::class)->find((int) ($arguments['profileId'] ?? 0));

        if (!$profile instanceof SocialNetworkProfile) {
            throw new McpToolException('Social-Network-Profil nicht gefunden.');
        }

        $this->deserializeInto(\is_array($arguments['profile'] ?? null) ? $arguments['profile'] : [], $profile);
        $this->flush();

        return $this->serializer->serialize($profile, 'json', []);
    }
}
