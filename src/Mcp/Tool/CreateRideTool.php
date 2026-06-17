<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\Ride;
use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Serializer\CriticalSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Write-Tool: spiegelt PUT /api/{citySlug}/{rideIdentifier} (Ride anlegen).
 */
final class CreateRideTool extends AbstractWriteTool
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
        return 'create_ride';
    }

    public function description(): string
    {
        return 'Legt einen neuen Ride in einer Stadt an.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'rideIdentifier' => ['type' => 'string', 'description' => 'Datum (YYYY-MM-DD) oder Slug des neuen Rides.'],
                'ride' => [
                    'type' => 'object',
                    'description' => 'Ride-Felder (api-write), z. B. title, description, dateTime, location, latitude, longitude.',
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
        $city = $this->resolver->city((string) ($arguments['citySlug'] ?? ''));
        $rideIdentifier = trim((string) ($arguments['rideIdentifier'] ?? ''));

        if ('' === $rideIdentifier) {
            throw new McpToolException('rideIdentifier ist erforderlich.');
        }

        /** @var Ride $ride */
        $ride = $this->deserialize(\is_array($arguments['ride'] ?? null) ? $arguments['ride'] : [], Ride::class);
        $ride->setCity($city);

        try {
            $dateTime = new \DateTime($rideIdentifier);

            if (!$ride->getDateTime()) {
                $ride->setDateTime($dateTime);
            }
        } catch (\Exception) {
            if (!$ride->hasSlug()) {
                $ride->setSlug($rideIdentifier);
            }
        }

        $this->validateEntity($ride);
        $this->persist($ride);
        $this->flush();

        return $this->serializer->serialize($ride, 'json', ['groups' => ['ride-list']]);
    }
}
