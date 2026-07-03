<?php declare(strict_types=1);

namespace Tests\Twig;

use App\Criticalmass\SocialNetwork\Network\NetworkInterface;
use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManagerInterface;
use App\Entity\SocialNetworkFeedItem;
use App\Entity\SocialNetworkProfile;
use App\Twig\Extension\SocialNetworkTwigExtension;
use PHPUnit\Framework\TestCase;

/**
 * Regression coverage for #1305: a feed item whose SocialNetworkProfile was
 * removed makes getSocialNetworkProfile() return null. networkIcon() then
 * looked the network up by a null identifier and called getIcon() on the
 * missing entry, raising a TypeError (500).
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

    public function testFeedItemWithoutProfileReturnsNoIconInsteadOfCrashing(): void
    {
        $feedItem = new SocialNetworkFeedItem();

        self::assertNull($feedItem->getSocialNetworkProfile());
        self::assertSame('', $this->createExtension()->networkIcon($feedItem));
    }

    public function testNullReturnsNoIcon(): void
    {
        self::assertSame('', $this->createExtension()->networkIcon(null));
    }

    public function testUnknownNetworkReturnsNoIcon(): void
    {
        self::assertSame('', $this->createExtension()->networkIcon('does-not-exist'));
    }

    public function testKnownNetworkReturnsItsIcon(): void
    {
        $profile = (new SocialNetworkProfile())->setNetwork('twitter');
        $feedItem = (new SocialNetworkFeedItem())->setSocialNetworkProfile($profile);

        $extension = $this->createExtension();

        self::assertSame('fa-twitter', $extension->networkIcon('twitter'));
        self::assertSame('fa-twitter', $extension->networkIcon($profile));
        self::assertSame('fa-twitter', $extension->networkIcon($feedItem));
    }
}
