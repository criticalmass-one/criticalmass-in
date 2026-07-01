<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\Post;
use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Serializer\CriticalSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Write-Tool: legt einen Forenbeitrag (Post) für eine Stadt (optional zu einem
 * Ride) an.
 */
final class CreatePostTool extends AbstractWriteTool
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
        return 'create_post';
    }

    public function description(): string
    {
        return 'Legt einen Forenbeitrag (Post) für eine Stadt an, optional einem Ride zugeordnet.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'rideIdentifier' => ['type' => 'string', 'description' => 'Optional: Ride-Datum (YYYY-MM-DD) oder -Slug.'],
                'post' => [
                    'type' => 'object',
                    'description' => 'Post-Felder: message (Pflicht), latitude, longitude, dateTime (ISO 8601, Standard jetzt).',
                ],
            ],
            'required' => ['citySlug', 'post'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::PostWrite;
    }

    public function call(array $arguments): string
    {
        $city = $this->resolver->city((string) ($arguments['citySlug'] ?? ''));
        $data = \is_array($arguments['post'] ?? null) ? $arguments['post'] : [];

        try {
            $dateTime = isset($data['dateTime']) ? new \DateTime((string) $data['dateTime']) : new \DateTime();
        } catch (\Exception) {
            throw new McpToolException('post.dateTime ist kein gültiger Zeitpunkt.');
        }

        $post = new Post();
        $post
            ->setCity($city)
            ->setMessage((string) ($data['message'] ?? ''))
            ->setDateTime($dateTime)
            ->setEnabled(true);

        if (isset($arguments['rideIdentifier']) && '' !== trim((string) $arguments['rideIdentifier'])) {
            $post->setRide($this->resolver->ride((string) $arguments['citySlug'], (string) $arguments['rideIdentifier']));
        }

        if (isset($data['latitude'])) {
            $post->setLatitude((float) $data['latitude']);
        }

        if (isset($data['longitude'])) {
            $post->setLongitude((float) $data['longitude']);
        }

        $this->validateEntity($post);
        $this->persist($post);
        $this->flush();

        return $this->serializer->serialize($post, 'json', ['groups' => ['post-list']]);
    }
}
