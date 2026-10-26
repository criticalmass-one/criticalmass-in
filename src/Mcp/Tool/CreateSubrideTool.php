<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\Subride;
use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Serializer\CriticalSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Write-Tool: legt einen Subride (Anfahrt/Teilstrecke) für einen Ride an.
 */
final class CreateSubrideTool extends AbstractWriteTool
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
        return 'create_subride';
    }

    public function description(): string
    {
        return 'Legt einen Subride (Anfahrt/Teilstrecke) für einen Ride an.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'rideIdentifier' => ['type' => 'string', 'description' => 'Ride-Datum (YYYY-MM-DD) oder -Slug.'],
                'subride' => [
                    'type' => 'object',
                    'description' => 'Subride-Felder: title (Pflicht), location (Pflicht), dateTime (Pflicht, ISO 8601), description, latitude, longitude.',
                ],
            ],
            'required' => ['citySlug', 'rideIdentifier', 'subride'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::SubrideWrite;
    }

    public function call(array $arguments): string
    {
        $ride = $this->resolver->ride((string) ($arguments['citySlug'] ?? ''), (string) ($arguments['rideIdentifier'] ?? ''));
        $data = \is_array($arguments['subride'] ?? null) ? $arguments['subride'] : [];

        if (!isset($data['dateTime'])) {
            throw new McpToolException('subride.dateTime ist erforderlich.');
        }

        try {
            $dateTime = new \DateTime((string) $data['dateTime']);
        } catch (\Exception) {
            throw new McpToolException('subride.dateTime ist kein gültiger Zeitpunkt.');
        }

        $subride = new Subride();
        $subride
            ->setRide($ride)
            ->setTitle((string) ($data['title'] ?? ''))
            ->setLocation((string) ($data['location'] ?? ''))
            ->setDateTime($dateTime)
            ->setDescription(isset($data['description']) ? (string) $data['description'] : null)
            ->setCreatedAt(new \DateTime());

        if (isset($data['latitude'])) {
            $subride->setLatitude((float) $data['latitude']);
        }

        if (isset($data['longitude'])) {
            $subride->setLongitude((float) $data['longitude']);
        }

        $this->validateEntity($subride);
        $this->persist($subride);
        $this->flush();

        return $this->serializer->serialize($subride, 'json', ['groups' => ['subride-list']]);
    }
}
