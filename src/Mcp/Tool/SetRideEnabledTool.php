<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Write-Tool: aktiviert bzw. deaktiviert einen Ride über das enabled-Flag.
 * Deaktivierte Rides bleiben erhalten, werden aber ausgeblendet (Soft-Delete).
 */
final class SetRideEnabledTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly ManagerRegistry $registry,
    ) {
    }

    public function name(): string
    {
        return 'set_ride_enabled';
    }

    public function description(): string
    {
        return 'Aktiviert oder deaktiviert einen Ride (enabled-Flag). enabled=false blendet den Ride aus (Soft-Delete).';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'rideIdentifier' => ['type' => 'string', 'description' => 'Ride-Datum (YYYY-MM-DD) oder -Slug.'],
                'enabled' => ['type' => 'boolean', 'description' => 'true = aktivieren, false = deaktivieren/ausblenden.'],
            ],
            'required' => ['citySlug', 'rideIdentifier', 'enabled'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::RideWrite;
    }

    public function call(array $arguments): string
    {
        if (!array_key_exists('enabled', $arguments) || !\is_bool($arguments['enabled'])) {
            throw new McpToolException('enabled muss ein Boolean sein (true oder false).');
        }

        $ride = $this->resolver->ride((string) ($arguments['citySlug'] ?? ''), (string) ($arguments['rideIdentifier'] ?? ''));
        $ride->setEnabled($arguments['enabled']);

        $this->registry->getManager()->flush();

        return json_encode([
            'status' => 'ok',
            'ride' => $ride->getTitle() ?? $ride->getDateTime()?->format('Y-m-d'),
            'enabled' => $ride->isEnabled(),
        ], JSON_THROW_ON_ERROR);
    }
}
