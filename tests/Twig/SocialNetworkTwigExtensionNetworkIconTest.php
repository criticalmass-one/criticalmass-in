<?php declare(strict_types=1);

namespace Tests\Twig;

use App\Criticalmass\SocialNetwork\Network\NetworkInterface;
use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManagerInterface;
use App\Entity\SocialNetworkProfile;
use App\Twig\Extension\SocialNetworkTwigExtension;
use PHPUnit\Framework\TestCase;

/**
 * Regression coverage for #1305: a null network identifier (e.g. a profile that
 * was removed, so a template passes item.socialNetworkProfile.network as null)
 * must not crash networkIcon() with getIcon()-on-null. Unknown networks fall
 * back to a generic icon.
 */
class SocialNetworkTwigExtensionNetworkIconTest extends TestCase
{
    private function createExtension(): SocialNetworkTwigExtension
    {
        $twitter = $this->createMock(NetworkInterface::class);
        $twitter->method('getIcon')->willReturn('fa-twitter');

        $networkManager = $this->createMock(NetworkManagerInterface::class);
        $networkManager->method('hasNetwork')->willReturnCallback(
            static fn (string $identifier): bool => $identifier === 'twitter'
        );
        $networkManager->method('getNetwork')->willReturnCallback(
            static fn (string $identifier): NetworkInterface => match ($identifier) {
                'twitter' => $twitter,
                default => throw new \OutOfBoundsException($identifier),
            }
        );

        return new SocialNetworkTwigExtension($networkManager);
    }

    public function testNullReturnsGenericIconInsteadOfCrashing(): void
    {
        self::assertSame('far fa-globe', $this->createExtension()->networkIcon(null));
    }

    public function testUnknownNetworkReturnsGenericIcon(): void
    {
        self::assertSame('far fa-globe', $this->createExtension()->networkIcon('does-not-exist'));
    }

    public function testKnownNetworkReturnsItsIcon(): void
    {
        $profile = (new SocialNetworkProfile())->setNetwork('twitter');

        $extension = $this->createExtension();

        self::assertSame('fa-twitter', $extension->networkIcon('twitter'));
        self::assertSame('fa-twitter', $extension->networkIcon($profile));
    }
}
