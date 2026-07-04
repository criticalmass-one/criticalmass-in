<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Serializer\CriticalSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Write-Tool: spiegelt POST /api/{citySlug}/{rideIdentifier} (Ride bearbeiten).
 */
final class UpdateRideTool extends AbstractWriteTool
{
    public function __construct(
        ManagerRegistry $registry,
        CriticalSerializerInterface $serializer,
        ValidatorInterface $validator,
        private readonly EntityResolver $resolver,
    ) {
        parent::__construct($registry, $serializer, $validator);
    }

    public function name(): string
    {
        return 'update_ride';
    }

    public function description(): string
    {
        return 'Aktualisiert einen bestehenden Ride.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'rideIdentifier' => ['type' => 'string', 'description' => 'Datum (YYYY-MM-DD) oder Slug des Rides.'],
                'ride' => [
                    'type' => 'object',
                    'description' => 'Zu ändernde Ride-Felder (api-write).',
                ],
            ],
            'required' => ['citySlug', 'rideIdentifier', 'ride'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::RideWrite;
    }

    public function call(array $arguments): string
    {
        $ride = $this->resolver->ride((string) ($arguments['citySlug'] ?? ''), (string) ($arguments['rideIdentifier'] ?? ''));

        $this->deserializeInto(\is_array($arguments['ride'] ?? null) ? $arguments['ride'] : [], $ride);

        $this->validateEntity($ride);
        $this->flush();

        return $this->serializer->serialize($ride, 'json', ['groups' => ['ride-list']]);
    }
}
