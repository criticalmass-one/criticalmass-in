<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Write-Tool: löscht einen Treffpunkt (Location) einer Stadt.
 */
final class DeleteLocationTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly ManagerRegistry $registry,
    ) {
    }

    public function name(): string
    {
        return 'delete_location';
    }

    public function description(): string
    {
        return 'Löscht einen Treffpunkt (Location) einer Stadt.';
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
        return OAuthScope::LocationWrite;
    }

    public function call(array $arguments): string
    {
        $location = $this->resolver->location(
            (string) ($arguments['citySlug'] ?? ''),
            (string) ($arguments['locationSlug'] ?? ''),
        );

        $slug = $location->getSlug();

        $manager = $this->registry->getManager();
        $manager->remove($location);
        $manager->flush();

        return json_encode(['status' => 'ok', 'deletedLocationSlug' => $slug], JSON_THROW_ON_ERROR);
    }
}
