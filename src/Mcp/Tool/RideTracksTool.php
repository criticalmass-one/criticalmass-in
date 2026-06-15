<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Repository\TrackRepository;
use App\Serializer\CriticalSerializerInterface;

/**
 * Read-Tool: spiegelt GET /api/{citySlug}/{rideIdentifier}/listTracks.
 */
final class RideTracksTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly TrackRepository $trackRepository,
        private readonly CriticalSerializerInterface $serializer,
    ) {
    }

    public function name(): string
    {
        return 'list_ride_tracks';
    }

    public function description(): string
    {
        return 'Listet die GPS-Tracks eines bestimmten Rides.';
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
        return OAuthScope::TrackRead;
    }

    public function call(array $arguments): string
    {
        $ride = $this->resolver->ride((string) ($arguments['citySlug'] ?? ''), (string) ($arguments['rideIdentifier'] ?? ''));
        $tracks = $this->trackRepository->findByRide($ride);

        return $this->serializer->serialize($tracks, 'json', ['groups' => ['api-public']]);
    }
}
