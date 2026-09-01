<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Entity\Weather;
use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Serializer\CriticalSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Write-Tool: spiegelt PUT /api/{citySlug}/{rideIdentifier}/weather.
 */
final class SetWeatherTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly ManagerRegistry $registry,
        private readonly CriticalSerializerInterface $serializer,
    ) {
    }

    public function name(): string
    {
        return 'set_weather';
    }

    public function description(): string
    {
        return 'Hinterlegt Wetterdaten für einen Ride.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'rideIdentifier' => ['type' => 'string', 'description' => 'Ride-Datum (YYYY-MM-DD) oder -Slug.'],
                'weather' => [
                    'type' => 'object',
                    'description' => 'Wetterdaten.',
                    'properties' => [
                        'temperatureMin' => ['type' => 'number'],
                        'temperatureMax' => ['type' => 'number'],
                        'temperatureDay' => ['type' => 'number'],
                        'pressure' => ['type' => 'number'],
                        'humidity' => ['type' => 'number'],
                        'weatherCode' => ['type' => 'integer'],
                        'weatherDateTime' => ['type' => 'string', 'description' => 'Zeitpunkt der Messung (ISO 8601).'],
                    ],
                ],
            ],
            'required' => ['citySlug', 'rideIdentifier', 'weather'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::WeatherWrite;
    }

    public function call(array $arguments): string
    {
        $ride = $this->resolver->ride((string) ($arguments['citySlug'] ?? ''), (string) ($arguments['rideIdentifier'] ?? ''));

        if (!\is_array($arguments['weather'] ?? null)) {
            throw new McpToolException('weather muss ein Objekt mit Wetterdaten sein.');
        }

        try {
            /** @var Weather $weather */
            $weather = $this->serializer->deserialize(
                json_encode($arguments['weather'], JSON_THROW_ON_ERROR),
                Weather::class,
                'json',
                ['groups' => ['weather']],
            );
        } catch (\Throwable $exception) {
            throw new McpToolException(sprintf('Wetterdaten konnten nicht gelesen werden: %s', $exception->getMessage()));
        }

        $weather
            ->setRide($ride)
            ->setCreationDateTime(new \DateTime());

        $manager = $this->registry->getManager();
        $manager->persist($weather);
        $manager->flush();

        return json_encode([
            'status' => 'ok',
            'ride' => $ride->getTitle() ?? $ride->getDateTime()?->format('Y-m-d'),
        ], JSON_THROW_ON_ERROR);
    }
}
