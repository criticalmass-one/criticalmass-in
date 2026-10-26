<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Event\RideEstimate\RideEstimateDeletedEvent;
use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Write-Tool: löscht eine Teilnehmerzahl-Schätzung (per ID). Feuert
 * RideEstimateDeletedEvent, damit die aggregierten Ride-Schätzungen neu
 * berechnet werden. IDs liefert list_ride_estimates.
 */
final class DeleteRideEstimateTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly ManagerRegistry $registry,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    public function name(): string
    {
        return 'delete_ride_estimate';
    }

    public function description(): string
    {
        return 'Löscht eine Teilnehmerzahl-Schätzung (per ID).';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'estimateId' => ['type' => 'integer', 'description' => 'ID der Schätzung (siehe list_ride_estimates).'],
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
        $id = $estimate->getId();

        $manager = $this->registry->getManager();
        $manager->remove($estimate);
        $manager->flush();

        // Nach dem Löschen feuern: der Handler berechnet die verbleibenden
        // Schätzungen des Rides neu (der Estimate hält die Ride-Referenz weiter).
        $this->eventDispatcher->dispatch(new RideEstimateDeletedEvent($estimate), RideEstimateDeletedEvent::NAME);

        return json_encode(['status' => 'ok', 'deletedId' => $id], JSON_THROW_ON_ERROR);
    }
}
