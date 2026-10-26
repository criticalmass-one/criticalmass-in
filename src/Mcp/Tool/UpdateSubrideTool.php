<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Serializer\CriticalSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Write-Tool: aktualisiert einen bestehenden Subride (per ID).
 */
final class UpdateSubrideTool extends AbstractWriteTool
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
        return 'update_subride';
    }

    public function description(): string
    {
        return 'Aktualisiert einen bestehenden Subride eines Rides (per ID).';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'rideIdentifier' => ['type' => 'string', 'description' => 'Ride-Datum (YYYY-MM-DD) oder -Slug.'],
                'subrideId' => ['type' => 'integer', 'description' => 'ID des Subrides.'],
                'subride' => [
                    'type' => 'object',
                    'description' => 'Zu ändernde Felder: title, location, dateTime (ISO 8601), description, latitude, longitude.',
                ],
            ],
            'required' => ['citySlug', 'rideIdentifier', 'subrideId', 'subride'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::SubrideWrite;
    }

    public function call(array $arguments): string
    {
        $subride = $this->resolver->subride(
            (string) ($arguments['citySlug'] ?? ''),
            (string) ($arguments['rideIdentifier'] ?? ''),
            (int) ($arguments['subrideId'] ?? 0),
        );

        $data = \is_array($arguments['subride'] ?? null) ? $arguments['subride'] : [];

        if (array_key_exists('title', $data)) {
            $subride->setTitle((string) $data['title']);
        }

        if (array_key_exists('location', $data)) {
            $subride->setLocation((string) $data['location']);
        }

        if (array_key_exists('description', $data)) {
            $subride->setDescription(null === $data['description'] ? null : (string) $data['description']);
        }

        if (array_key_exists('dateTime', $data)) {
            try {
                $subride->setDateTime(new \DateTime((string) $data['dateTime']));
            } catch (\Exception) {
                throw new McpToolException('subride.dateTime ist kein gültiger Zeitpunkt.');
            }
        }

        if (array_key_exists('latitude', $data)) {
            $subride->setLatitude((float) $data['latitude']);
        }

        if (array_key_exists('longitude', $data)) {
            $subride->setLongitude((float) $data['longitude']);
        }

        $this->validateEntity($subride);
        $this->flush();

        return $this->serializer->serialize($subride, 'json', ['groups' => ['subride-list']]);
    }
}
