<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\Location;
use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Serializer\CriticalSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Write-Tool: legt einen Treffpunkt (Location) für eine Stadt an.
 */
final class CreateLocationTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly ManagerRegistry $registry,
        private readonly SluggerInterface $slugger,
        private readonly CriticalSerializerInterface $serializer,
    ) {
    }

    public function name(): string
    {
        return 'create_location';
    }

    public function description(): string
    {
        return 'Legt einen Treffpunkt (Location) für eine Stadt an.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'location' => [
                    'type' => 'object',
                    'description' => 'Location-Felder: title (Pflicht), slug (optional, wird sonst aus dem Titel abgeleitet), latitude, longitude, description.',
                ],
            ],
            'required' => ['citySlug', 'location'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::LocationWrite;
    }

    public function call(array $arguments): string
    {
        $city = $this->resolver->city((string) ($arguments['citySlug'] ?? ''));
        $data = \is_array($arguments['location'] ?? null) ? $arguments['location'] : [];

        $title = trim((string) ($data['title'] ?? ''));

        if ('' === $title) {
            throw new McpToolException('location.title ist erforderlich.');
        }

        $slug = trim((string) ($data['slug'] ?? ''));
        if ('' === $slug) {
            $slug = strtolower((string) $this->slugger->slug($title));
        }

        $repository = $this->registry->getRepository(Location::class);

        if (null !== $repository->findOneBy(['city' => $city, 'slug' => $slug])) {
            throw new McpToolException(sprintf('In dieser Stadt existiert bereits eine Location mit dem Slug "%s".', $slug));
        }

        $location = new Location();
        $location
            ->setCity($city)
            ->setTitle($title)
            ->setSlug($slug)
            ->setDescription(isset($data['description']) ? (string) $data['description'] : null)
            ->setLatitude(isset($data['latitude']) ? (float) $data['latitude'] : null)
            ->setLongitude(isset($data['longitude']) ? (float) $data['longitude'] : null);

        $manager = $this->registry->getManager();
        $manager->persist($location);
        $manager->flush();

        return $this->serializer->serialize($location, 'json', []);
    }
}
