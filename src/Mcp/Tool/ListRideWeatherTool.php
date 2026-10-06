<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\Weather;
use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Serializer\CriticalSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Read-Tool: listet die Wetter-Einträge eines Rides inkl. ihrer IDs.
 * Die IDs werden von update_weather / delete_weather benötigt.
 */
final class ListRideWeatherTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly ManagerRegistry $registry,
        private readonly CriticalSerializerInterface $serializer,
    ) {
    }

    public function name(): string
    {
        return 'list_ride_weather';
    }

    public function description(): string
    {
        return 'Listet die Wetter-Einträge eines Rides mit ihren IDs.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'rideIdentifier' => ['type' => 'string', 'description' => 'Ride-Datum (YYYY-MM-DD) oder -Slug.'],
            ],
            'required' => ['citySlug', 'rideIdentifier'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::RideRead;
    }

    public function call(array $arguments): string
    {
        $ride = $this->resolver->ride((string) ($arguments['citySlug'] ?? ''), (string) ($arguments['rideIdentifier'] ?? ''));

        $weathers = $this->registry->getRepository(Weather::class)->findBy(['ride' => $ride]);

        return $this->serializer->serialize($weathers, 'json', ['groups' => ['weather']]);
    }
}
