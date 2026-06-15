<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Repository\RideRepository;
use App\Serializer\CriticalSerializerInterface;

/**
 * Read-Tool: spiegelt GET /api/{citySlug}/current (nächster/aktueller Ride).
 */
final class GetCurrentRideTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly RideRepository $rideRepository,
        private readonly CriticalSerializerInterface $serializer,
    ) {
    }

    public function name(): string
    {
        return 'get_current_ride';
    }

    public function description(): string
    {
        return 'Liefert den aktuellen bzw. nächsten Ride einer Stadt.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt, z. B. "hamburg".'],
                'cycleMandatory' => ['type' => 'boolean', 'description' => 'Nur Rides aus einem Cycle berücksichtigen.'],
                'slugsAllowed' => ['type' => 'boolean', 'description' => 'Auch Rides mit Slug zulassen.'],
            ],
            'required' => ['citySlug'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::RideRead;
    }

    public function call(array $arguments): string
    {
        $city = $this->resolver->city((string) ($arguments['citySlug'] ?? ''));

        $ride = $this->rideRepository->findCurrentRideForCity(
            $city,
            (bool) ($arguments['cycleMandatory'] ?? false),
            (bool) ($arguments['slugsAllowed'] ?? true),
        );

        if (null === $ride) {
            throw new McpToolException('Für diese Stadt gibt es aktuell keinen Ride.');
        }

        return $this->serializer->serialize($ride, 'json', ['groups' => ['ride-details']]);
    }
}
