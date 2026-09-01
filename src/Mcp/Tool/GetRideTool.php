<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Serializer\CriticalSerializerInterface;

/**
 * Read-Tool: spiegelt GET /api/{citySlug}/{rideIdentifier} (Ride-Details).
 */
final class GetRideTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly CriticalSerializerInterface $serializer,
    ) {
    }

    public function name(): string
    {
        return 'get_ride';
    }

    public function description(): string
    {
        return 'Liefert die Details eines einzelnen Rides anhand von Stadt und Identifier (Datum oder Slug).';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt, z. B. "hamburg".'],
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

        return $this->serializer->serialize($ride, 'json', ['groups' => ['ride-details']]);
    }
}
