<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Event\RideEstimate\RideEstimateUpdatedEvent;
use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Write-Tool: aktualisiert eine bestehende Teilnehmerzahl-Schätzung (per ID).
 * Feuert RideEstimateUpdatedEvent, damit die aggregierten Ride-Schätzungen
 * neu berechnet werden. IDs liefert list_ride_estimates.
 */
final class UpdateRideEstimateTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly ManagerRegistry $registry,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function name(): string
    {
        return 'update_ride_estimate';
    }

    public function description(): string
    {
        return 'Aktualisiert eine bestehende Teilnehmerzahl-Schätzung (per ID).';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'estimateId' => ['type' => 'integer', 'description' => 'ID der Schätzung (siehe list_ride_estimates).'],
                'estimation' => ['type' => 'integer', 'description' => 'Neue geschätzte Teilnehmerzahl.', 'minimum' => 0],
                'latitude' => ['type' => 'number', 'description' => 'Neuer Breitengrad der Schätzung.'],
                'longitude' => ['type' => 'number', 'description' => 'Neuer Längengrad der Schätzung.'],
                'dateTime' => ['type' => 'string', 'description' => 'Neuer Zeitpunkt der Schätzung (ISO 8601).'],
                'source' => ['type' => 'string', 'description' => 'Neue Quelle der Schätzung.'],
            ],
            'required' => ['estimateId'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::EstimateWrite;
    }

    public function call(array $arguments): string
    {
        if (!isset($arguments['estimateId']) || !is_numeric($arguments['estimateId'])) {
            throw new McpToolException('estimateId ist erforderlich und muss eine Zahl sein.');
        }

        $estimate = $this->resolver->rideEstimate((int) $arguments['estimateId']);

        if (array_key_exists('estimation', $arguments)) {
            if ((int) $arguments['estimation'] < 0) {
                throw new McpToolException('estimation muss eine nichtnegative Zahl sein.');
            }
            $estimate->setEstimatedParticipants((int) $arguments['estimation']);
        }

        if (array_key_exists('latitude', $arguments)) {
            $estimate->setLatitude(null === $arguments['latitude'] ? null : (float) $arguments['latitude']);
        }

        if (array_key_exists('longitude', $arguments)) {
            $estimate->setLongitude(null === $arguments['longitude'] ? null : (float) $arguments['longitude']);
        }

        if (array_key_exists('dateTime', $arguments)) {
            try {
                $estimate->setDateTime(new \DateTime((string) $arguments['dateTime']));
            } catch (\Exception) {
                throw new McpToolException('dateTime ist kein gültiger Zeitpunkt.');
            }
        }

        if (array_key_exists('source', $arguments)) {
            $estimate->setSource(null === $arguments['source'] ? null : (string) $arguments['source']);
        }

        $this->registry->getManager()->flush();

        $this->eventDispatcher->dispatch(new RideEstimateUpdatedEvent($estimate), RideEstimateUpdatedEvent::NAME);

        return json_encode([
            'status' => 'ok',
            'id' => $estimate->getId(),
            'estimation' => $estimate->getEstimatedParticipants(),
        ], JSON_THROW_ON_ERROR);
    }
}
