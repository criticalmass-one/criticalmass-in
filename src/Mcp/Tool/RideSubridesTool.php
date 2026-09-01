<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Repository\SubrideRepository;
use App\Serializer\CriticalSerializerInterface;

/**
 * Read-Tool: spiegelt GET /api/{citySlug}/{rideIdentifier}/subride.
 */
final class RideSubridesTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly SubrideRepository $subrideRepository,
        private readonly CriticalSerializerInterface $serializer,
    ) {
    }

    public function name(): string
    {
        return 'list_subrides';
    }

    public function description(): string
    {
        return 'Listet die Subrides (Anfahrtsrouten) eines bestimmten Rides.';
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
        $subrides = $this->subrideRepository->getSubridesForRide($ride);

        return $this->serializer->serialize($subrides, 'json', []);
    }
}
