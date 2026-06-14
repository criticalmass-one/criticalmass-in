<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Criticalmass\Participation\Manager\ParticipationManagerInterface;
use App\OAuth2\OAuthScope;
use App\Repository\RideRepository;

/**
 * Write-Tool: meldet die Teilnahme des authentifizierten Users an einem Ride.
 * Der User stammt aus dem OAuth2-Token (Security-Context); der
 * ParticipationManager liest ihn intern aus dem TokenStorage.
 */
final class SetParticipationTool implements McpToolInterface
{
    private const STATUSES = ['yes', 'maybe', 'no'];

    public function __construct(
        private readonly RideRepository $rideRepository,
        private readonly ParticipationManagerInterface $participationManager,
    ) {
    }

    public function name(): string
    {
        return 'set_participation';
    }

    public function description(): string
    {
        return 'Meldet die eigene Teilnahme an einem Ride: "yes", "maybe" oder "no".';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => [
                    'type' => 'string',
                    'description' => 'Slug der Stadt, z. B. "hamburg".',
                ],
                'rideIdentifier' => [
                    'type' => 'string',
                    'description' => 'Datum des Rides (YYYY-MM-DD) oder dessen Slug.',
                ],
                'status' => [
                    'type' => 'string',
                    'enum' => self::STATUSES,
                    'description' => 'Teilnahmestatus.',
                ],
            ],
            'required' => ['citySlug', 'rideIdentifier', 'status'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::ParticipationWrite;
    }

    public function call(array $arguments): string
    {
        $citySlug = trim((string) ($arguments['citySlug'] ?? ''));
        $rideIdentifier = trim((string) ($arguments['rideIdentifier'] ?? ''));
        $status = (string) ($arguments['status'] ?? '');

        if ('' === $citySlug || '' === $rideIdentifier) {
            throw new McpToolException('citySlug und rideIdentifier sind erforderlich.');
        }

        if (!in_array($status, self::STATUSES, true)) {
            throw new McpToolException('status muss "yes", "maybe" oder "no" sein.');
        }

        $ride = $this->resolveRide($citySlug, $rideIdentifier);

        if (null === $ride) {
            throw new McpToolException(sprintf('Kein Ride gefunden für "%s/%s".', $citySlug, $rideIdentifier));
        }

        $this->participationManager->participate($ride, $status);

        return json_encode([
            'status' => 'ok',
            'ride' => $ride->getTitle() ?? $ride->getDateTime()?->format('Y-m-d'),
            'participation' => $status,
        ], JSON_THROW_ON_ERROR);
    }

    private function resolveRide(string $citySlug, string $rideIdentifier): ?\App\Entity\Ride
    {
        try {
            $ride = $this->rideRepository->findByCitySlugAndRideDate($citySlug, $rideIdentifier);
        } catch (\Exception) {
            // rideIdentifier ist kein gültiges Datum → als Slug behandeln.
            $ride = null;
        }

        return $ride ?? $this->rideRepository->findOneByCitySlugAndSlug($citySlug, $rideIdentifier);
    }
}
