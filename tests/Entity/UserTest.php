<?php declare(strict_types=1);

namespace Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    #[Test]
    public function everyUserGetsRoleUserExactlyOnce(): void
    {
        self::assertSame(['ROLE_USER'], (new User())->getRoles());
        self::assertSame(['ROLE_USER'], (new User())->setRoles(['ROLE_USER'])->getRoles());
        self::assertSame(['ROLE_ADMIN', 'ROLE_USER'], (new User())->setRoles(['ROLE_ADMIN'])->getRoles());
    }

    #[Test]
    public function hasRoleOnlyLooksAtExplicitlyAssignedRoles(): void
    {
        $user = (new User())->setRoles(['ROLE_ADMIN']);

        self::assertTrue($user->hasRole('ROLE_ADMIN'));
        self::assertFalse($user->hasRole('ROLE_MODERATOR'));
        // The implicit ROLE_USER from getRoles() is not visible to hasRole().
        self::assertFalse($user->hasRole('ROLE_USER'));
    }

    #[Test]
    public function oauthAccountsAreDetectedByProviderIds(): void
    {
        self::assertFalse((new User())->isOauthAccount());
        self::assertTrue((new User())->setFacebookId('123')->isFacebookAccount());
        self::assertTrue((new User())->setFacebookId('123')->isOauthAccount());
        self::assertTrue((new User())->setStravaId('456')->isStravaAccount());
        self::assertTrue((new User())->setStravaId('456')->isOauthAccount());
    }

    #[Test]
    public function userIdentifierIsTheEmailNotTheUsername(): void
    {
        $user = (new User())->setUsername('malte')->setEmail('malte@example.org');

        self::assertSame('malte@example.org', $user->getUserIdentifier());
        self::assertSame('', (new User())->setUsername('malte')->getUserIdentifier());
    }
}
