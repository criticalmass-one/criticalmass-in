<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Write-Tool: löscht einen Subride eines Rides (per ID).
 */
final class DeleteSubrideTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly ManagerRegistry $registry,
    ) {
    }

    public function name(): string
    {
        return 'delete_subride';
    }

    public function description(): string
    {
        return 'Löscht einen Subride eines Rides (per ID).';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'rideIdentifier' => ['type' => 'string', 'description' => 'Ride-Datum (YYYY-MM-DD) oder -Slug.'],
                'subrideId' => ['type' => 'integer', 'description' => 'ID des Subrides.'],
            ],
            'required' => ['citySlug', 'rideIdentifier', 'subrideId'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::SubrideWrite;
    }

    public function call(array $arguments): string
    {
        $subride = $this->resolver->subride(
            (string) ($arguments['citySlug'] ?? ''),
            (string) ($arguments['rideIdentifier'] ?? ''),
            (int) ($arguments['subrideId'] ?? 0),
        );

        $id = $subride->getId();

        $manager = $this->registry->getManager();
        $manager->remove($subride);
        $manager->flush();

        return json_encode(['status' => 'ok', 'deletedSubrideId' => $id], JSON_THROW_ON_ERROR);
    }
}
