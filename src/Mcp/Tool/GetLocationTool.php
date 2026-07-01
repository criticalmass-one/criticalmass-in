<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use App\Serializer\CriticalSerializerInterface;

/**
 * Read-Tool: spiegelt GET /api/{citySlug}/location/{slug} (einzelne Location).
 */
final class GetLocationTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly CriticalSerializerInterface $serializer,
    ) {
    }

    public function name(): string
    {
        return 'get_location';
    }

    public function description(): string
    {
        return 'Liefert die Details eines Treffpunkts (Location) einer Stadt.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'locationSlug' => ['type' => 'string', 'description' => 'Slug der Location.'],
            ],
            'required' => ['citySlug', 'locationSlug'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::CityRead;
    }

    public function call(array $arguments): string
    {
        $location = $this->resolver->location(
            (string) ($arguments['citySlug'] ?? ''),
            (string) ($arguments['locationSlug'] ?? ''),
        );

        return $this->serializer->serialize($location, 'json', []);
    }
}
