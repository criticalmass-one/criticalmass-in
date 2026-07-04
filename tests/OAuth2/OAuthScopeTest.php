<?php declare(strict_types=1);

namespace Tests\OAuth2;

use App\OAuth2\OAuthScope;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

/**
 * Stellt sicher, dass das OAuthScope-Enum (Single Source of Truth) nicht von
 * der in league_oauth2_server.yaml konfigurierten Scope-Liste abdriftet.
 */
final class OAuthScopeTest extends TestCase
{
    public function testEnumMatchesConfiguredAvailableScopes(): void
    {
        $config = Yaml::parseFile(__DIR__ . '/../../config/packages/league_oauth2_server.yaml');
        $available = $config['league_oauth2_server']['scopes']['available'];

        self::assertSame(OAuthScope::values(), $available);
    }
}
