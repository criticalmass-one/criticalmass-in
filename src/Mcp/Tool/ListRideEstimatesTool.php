<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Repository\RideEstimateRepository;

/**
 * Read-Tool: listet die Teilnehmerzahl-Schätzungen eines Rides inkl. ihrer IDs.
 * Die IDs werden von update_ride_estimate / delete_ride_estimate benötigt.
 */
final class ListRideEstimatesTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly RideEstimateRepository $repository,
    ) {
    }

    public function name(): string
    {
        return 'list_ride_estimates';
    }

    public function description(): string
    {
        return 'Listet alle Teilnehmerzahl-Schätzungen eines Rides mit ihren IDs.';
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

        $estimates = [];
        foreach ($this->repository->findEstimatesByRide($ride) as $estimate) {
            $estimates[] = [
                'id' => $estimate->getId(),
                'estimation' => $estimate->getEstimatedParticipants(),
                'latitude' => $estimate->getLatitude(),
                'longitude' => $estimate->getLongitude(),
                'dateTime' => $estimate->getDateTime()->format(\DateTimeInterface::ATOM),
                'source' => $estimate->getSource(),
                'user' => $estimate->getUser()?->getUsername(),
            ];
        }

        return json_encode(['estimates' => $estimates], JSON_THROW_ON_ERROR);
    }
}
