<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\Location;
use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Serializer\CriticalSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Write-Tool: aktualisiert einen bestehenden Treffpunkt (Location).
 */
final class UpdateLocationTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly ManagerRegistry $registry,
        private readonly CriticalSerializerInterface $serializer,
    ) {
    }

    public function name(): string
    {
        return 'update_location';
    }

    public function description(): string
    {
        return 'Aktualisiert einen bestehenden Treffpunkt (Location) einer Stadt.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'locationSlug' => ['type' => 'string', 'description' => 'Slug der Location.'],
                'location' => [
                    'type' => 'object',
                    'description' => 'Zu ändernde Felder: title, latitude, longitude, description.',
                ],
            ],
            'required' => ['citySlug', 'locationSlug', 'location'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::LocationWrite;
    }

    public function call(array $arguments): string
    {
        $location = $this->resolver->location(
            (string) ($arguments['citySlug'] ?? ''),
            (string) ($arguments['locationSlug'] ?? ''),
        );

        $data = \is_array($arguments['location'] ?? null) ? $arguments['location'] : [];

        if (array_key_exists('title', $data)) {
            $title = trim((string) $data['title']);
            if ('' === $title) {
                throw new McpToolException('location.title darf nicht leer sein.');
            }
            $location->setTitle($title);
        }

        if (array_key_exists('description', $data)) {
            $location->setDescription(null === $data['description'] ? null : (string) $data['description']);
        }

        if (array_key_exists('latitude', $data)) {
            $location->setLatitude(null === $data['latitude'] ? null : (float) $data['latitude']);
        }

        if (array_key_exists('longitude', $data)) {
            $location->setLongitude(null === $data['longitude'] ? null : (float) $data['longitude']);
        }

        $this->registry->getManager()->flush();

        return $this->serializer->serialize($location, 'json', []);
    }
}
