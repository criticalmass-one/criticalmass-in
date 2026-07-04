<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Repository\LocationRepository;
use App\Serializer\CriticalSerializerInterface;

/**
 * Read-Tool: spiegelt GET /api/{citySlug}/location.
 */
final class CityLocationsTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly LocationRepository $locationRepository,
        private readonly CriticalSerializerInterface $serializer,
    ) {
    }

    public function name(): string
    {
        return 'list_locations';
    }

    public function description(): string
    {
        return 'Listet die Treffpunkte (Locations) einer Stadt.';
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
        $locations = $this->locationRepository->findLocationsByCity($city);

        return $this->serializer->serialize($locations, 'json', []);
    }
}
