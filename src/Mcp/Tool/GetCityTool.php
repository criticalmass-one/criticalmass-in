<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Serializer\CriticalSerializerInterface;

/**
 * Read-Tool: spiegelt GET /api/{citySlug} (Stadt-Details).
 */
final class GetCityTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly CriticalSerializerInterface $serializer,
    ) {
    }

    public function name(): string
    {
        return 'get_city';
    }

    public function description(): string
    {
        return 'Liefert die Details einer Stadt anhand ihres Slugs.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt, z. B. "hamburg".'],
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

        return $this->serializer->serialize($city, 'json', ['groups' => ['ride-list']]);
    }
}
