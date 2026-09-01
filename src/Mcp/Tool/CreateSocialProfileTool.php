<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\SocialNetworkProfile;
use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Serializer\CriticalSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Write-Tool: spiegelt PUT /api/{citySlug}/socialnetwork-profiles.
 */
final class CreateSocialProfileTool extends AbstractWriteTool
{
    public function __construct(
        ManagerRegistry $registry,
        CriticalSerializerInterface $serializer,
        ValidatorInterface $validator,
        private readonly EntityResolver $resolver,
    ) {
        parent::__construct($registry, $serializer, $validator);
    }

    public function name(): string
    {
        return 'create_social_profile';
    }

    public function description(): string
    {
        return 'Legt ein Social-Network-Profil für eine Stadt an.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'profile' => [
                    'type' => 'object',
                    'description' => 'Profil-Felder (api-write), z. B. networkIdentifier, identifier, name.',
                ],
            ],
            'required' => ['citySlug', 'profile'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::SocialNetworkWrite;
    }

    public function call(array $arguments): string
    {
        $city = $this->resolver->city((string) ($arguments['citySlug'] ?? ''));

        /** @var SocialNetworkProfile $profile */
        $profile = $this->deserialize(\is_array($arguments['profile'] ?? null) ? $arguments['profile'] : [], SocialNetworkProfile::class);
        $profile
            ->setCity($city)
            ->setCreatedAt(new \DateTime());

        $this->persist($profile);
        $this->flush();

        return $this->serializer->serialize($profile, 'json', []);
    }
}
