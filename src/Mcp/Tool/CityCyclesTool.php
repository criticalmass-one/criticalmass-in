<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Repository\CityCycleRepository;
use App\Serializer\CriticalSerializerInterface;

/**
 * Read-Tool: spiegelt GET /api/{citySlug}/cycles (regelmäßige Termine).
 */
final class CityCyclesTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly CityCycleRepository $cityCycleRepository,
        private readonly CriticalSerializerInterface $serializer,
    ) {
    }

    public function name(): string
    {
        return 'list_city_cycles';
    }

    public function description(): string
    {
        return 'Listet die wiederkehrenden Termin-Zyklen (Cycles) einer Stadt.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
            ],
            'required' => ['citySlug'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::CityRead;
    }

    public function call(array $arguments): string
    {
        $city = $this->resolver->city((string) ($arguments['citySlug'] ?? ''));
        $cycles = $this->cityCycleRepository->findByCity($city);

        return $this->serializer->serialize($cycles, 'json', []);
    }
}
