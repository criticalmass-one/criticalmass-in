<?php declare(strict_types=1);

namespace App\Mcp\Tool;

use App\Mcp\Support\EntityResolver;
use App\OAuth2\OAuthScope;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Write-Tool: aktiviert bzw. deaktiviert eine Stadt über das enabled-Flag.
 * Deaktivierte Städte bleiben erhalten, werden aber ausgeblendet.
 */
final class SetCityEnabledTool implements McpToolInterface
{
    public function __construct(
        private readonly EntityResolver $resolver,
        private readonly ManagerRegistry $registry,
    ) {
    }

    public function name(): string
    {
        return 'set_city_enabled';
    }

    public function description(): string
    {
        return 'Aktiviert oder deaktiviert eine Stadt (enabled-Flag). enabled=false blendet die Stadt aus.';
    }

    public function inputSchema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'citySlug' => ['type' => 'string', 'description' => 'Slug der Stadt.'],
                'enabled' => ['type' => 'boolean', 'description' => 'true = aktivieren, false = deaktivieren.'],
            ],
            'required' => ['citySlug', 'enabled'],
        ];
    }

    public function requiredScope(): OAuthScope
    {
        return OAuthScope::CityWrite;
    }

    public function call(array $arguments): string
    {
        if (!array_key_exists('enabled', $arguments) || !\is_bool($arguments['enabled'])) {
            throw new McpToolException('enabled muss ein Boolean sein (true oder false).');
        }

        $city = $this->resolver->city((string) ($arguments['citySlug'] ?? ''));
        $city->setEnabled($arguments['enabled']);

        $this->registry->getManager()->flush();

        return json_encode([
            'status' => 'ok',
            'city' => $city->getMainSlugString(),
            'enabled' => $city->isEnabled(),
        ], JSON_THROW_ON_ERROR);
    }
}
