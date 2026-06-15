<?php declare(strict_types=1);

namespace Tests\OAuth2;

use App\OAuth2\OAuthScope;
use PHPUnit\Framework\TestCase;

/**
 * Unit-Tests des OAuthScope-Enums (Single Source of Truth der Scopes).
 */
final class OAuthScopeUnitTest extends TestCase
{
    public function testValuesMatchEnumCases(): void
    {
        self::assertCount(\count(OAuthScope::cases()), OAuthScope::values());
        self::assertContains('ride:read', OAuthScope::values());
        self::assertContains('activity:write', OAuthScope::values());
    }

    public function testValuesAreUnique(): void
    {
        $values = OAuthScope::values();

        self::assertSame(array_values(array_unique($values)), $values);
    }

    public function testFilterKnownKeepsKnownAndDropsUnknown(): void
    {
        $result = OAuthScope::filterKnown(['ride:read', 'bogus', 'track:write']);

        self::assertSame(['ride:read', 'track:write'], $result);
    }

    public function testFilterKnownReturnsEmptyForOnlyUnknown(): void
    {
        self::assertSame([], OAuthScope::filterKnown(['nope', 'also-nope']));
    }

    public function testFilterKnownEmptyInput(): void
    {
        self::assertSame([], OAuthScope::filterKnown([]));
    }

    public function testEveryCaseHasNonEmptyLabel(): void
    {
        foreach (OAuthScope::cases() as $scope) {
            self::assertNotSame('', $scope->label(), $scope->value . ' braucht ein Label');
        }
    }

    public function testScopeValuesUseResourceActionFormat(): void
    {
        foreach (OAuthScope::values() as $value) {
            self::assertMatchesRegularExpression('/^[a-z]+:(read|write)$/', $value);
        }
    }
}
