<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Write-Tool: löscht einen Wetter-Eintrag (per ID).
 */
final class DeleteWeatherTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly ManagerRegistry $registry,
    ) {
    }

    public function name(): string
    {
        return 'delete_weather';
    }

    public function description(): string
    {
        return 'Löscht einen Wetter-Eintrag eines Rides (per ID).';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'weatherId' => ['type' => 'integer', 'description' => 'ID des Wetter-Eintrags (siehe list_ride_weather).'],
            ],
            'required' => ['weatherId'],
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
        $id = $weather->getId();

        $manager = $this->registry->getManager();
        $manager->remove($weather);
        $manager->flush();

        return json_encode(['status' => 'ok', 'deletedWeatherId' => $id], JSON_THROW_ON_ERROR);
    }
}
