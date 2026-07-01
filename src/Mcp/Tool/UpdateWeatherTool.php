<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Serializer\CriticalSerializerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Write-Tool: aktualisiert einen bestehenden Wetter-Eintrag (per ID).
 */
final class UpdateWeatherTool extends AbstractWriteTool
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
        return 'update_weather';
    }

    public function description(): string
    {
        return 'Aktualisiert einen bestehenden Wetter-Eintrag eines Rides (per ID).';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'weatherId' => ['type' => 'integer', 'description' => 'ID des Wetter-Eintrags (siehe list_ride_weather).'],
                'weather' => [
                    'type' => 'object',
                    'description' => 'Zu ändernde Wetter-Felder (Gruppe weather), z. B. temperatureMin, temperatureMax, pressure, humidity.',
                ],
            ],
            'required' => ['weatherId', 'weather'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::WeatherWrite;
    }

    public function call(array $arguments): string
    {
        if (!isset($arguments['weatherId']) || !is_numeric($arguments['weatherId'])) {
            throw new McpToolException('weatherId ist erforderlich und muss eine Zahl sein.');
        }

        $weather = $this->resolver->weather((int) $arguments['weatherId']);

        $this->deserializeInto(
            \is_array($arguments['weather'] ?? null) ? $arguments['weather'] : [],
            $weather,
            ['weather'],
        );

        $this->validateEntity($weather);
        $this->flush();

        return $this->serializer->serialize($weather, 'json', ['groups' => ['weather']]);
    }
}
