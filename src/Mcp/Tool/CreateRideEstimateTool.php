<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\RideEstimate;
use App\Event\RideEstimate\RideEstimateCreatedEvent;
use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Write-Tool: spiegelt POST /api/estimate (Teilnehmerzahl-Schätzung für einen Ride).
 */
final class CreateRideEstimateTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly ManagerRegistry $registry,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function name(): string
    {
        return 'create_ride_estimate';
    }

    public function description(): string
    {
        return 'Meldet eine geschätzte Teilnehmerzahl für einen Ride.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'rideIdentifier' => ['type' => 'string', 'description' => 'Ride-Datum (YYYY-MM-DD) oder -Slug.'],
                'estimation' => ['type' => 'integer', 'description' => 'Geschätzte Teilnehmerzahl.', 'minimum' => 0],
                'latitude' => ['type' => 'number', 'description' => 'Optionaler Breitengrad der Schätzung.'],
                'longitude' => ['type' => 'number', 'description' => 'Optionaler Längengrad der Schätzung.'],
                'dateTime' => ['type' => 'string', 'description' => 'Zeitpunkt der Schätzung (ISO 8601, Standard jetzt).'],
            ],
            'required' => ['citySlug', 'rideIdentifier', 'estimation'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::EstimateWrite;
    }

    public function call(array $arguments): string
    {
        $ride = $this->resolver->ride((string) ($arguments['citySlug'] ?? ''), (string) ($arguments['rideIdentifier'] ?? ''));

        if (!isset($arguments['estimation']) || (int) $arguments['estimation'] < 0) {
            throw new McpToolException('estimation muss eine nichtnegative Zahl sein.');
        }

        try {
            $dateTime = isset($arguments['dateTime']) ? new \DateTime((string) $arguments['dateTime']) : new \DateTime();
        } catch (\Exception) {
            throw new McpToolException('dateTime ist kein gültiger Zeitpunkt.');
        }

        $estimate = new RideEstimate();
        $estimate
            ->setEstimatedParticipants((int) $arguments['estimation'])
            ->setLatitude(isset($arguments['latitude']) ? (float) $arguments['latitude'] : null)
            ->setLongitude(isset($arguments['longitude']) ? (float) $arguments['longitude'] : null)
            ->setDateTime($dateTime)
            ->setSource('mcp')
            ->setRide($ride);

        $manager = $this->registry->getManager();
        $manager->persist($estimate);
        $manager->flush();

        $this->eventDispatcher->dispatch(new RideEstimateCreatedEvent($estimate), RideEstimateCreatedEvent::NAME);

        return json_encode([
            'status' => 'ok',
            'ride' => $ride->getTitle() ?? $ride->getDateTime()?->format('Y-m-d'),
            'estimation' => (int) $arguments['estimation'],
        ], JSON_THROW_ON_ERROR);
    }
}
