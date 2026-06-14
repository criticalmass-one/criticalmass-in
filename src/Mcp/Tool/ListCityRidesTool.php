<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\CitySlug;
use App\OAuth2\OAuthScope;
use App\Repository\RideRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Read-Tool: listet die Critical-Mass-Termine (Rides) einer Stadt.
 */
final class ListCityRidesTool implements McpToolInterface
{
    private const DEFAULT_LIMIT = 10;
    private const MAX_LIMIT = 50;

    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly RideRepository $rideRepository,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function name(): string
    {
        return 'list_city_rides';
    }

    public function description(): string
    {
        return 'Listet die Critical-Mass-Termine (Rides) einer Stadt anhand ihres Slugs, neueste zuerst.';
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
                'limit' => [
                    'type' => 'integer',
                    'description' => 'Maximale Anzahl Rides (1–50, Standard 10).',
                    'minimum' => 1,
                    'maximum' => self::MAX_LIMIT,
                ],
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
        $citySlug = trim((string) ($arguments['citySlug'] ?? ''));

        if ('' === $citySlug) {
            throw new McpToolException('citySlug ist erforderlich.');
        }

        $slug = $this->registry->getRepository(CitySlug::class)->findOneBy(['slug' => $citySlug]);
        $city = $slug?->getCity();

        if (null === $city) {
            throw new McpToolException(sprintf('Unbekannte Stadt: "%s".', $citySlug));
        }

        $limit = (int) ($arguments['limit'] ?? self::DEFAULT_LIMIT);
        $limit = max(1, min(self::MAX_LIMIT, $limit));

        $rides = $this->rideRepository->findRidesForCity($city, 'DESC', $limit);

        return $this->serializer->serialize($rides, 'json', ['groups' => ['ride-list']]);
    }
}
