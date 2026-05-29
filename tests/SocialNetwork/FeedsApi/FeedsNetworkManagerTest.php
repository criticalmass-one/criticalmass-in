<?php declare(strict_types=1);

namespace Tests\SocialNetwork\FeedsApi;

use App\Criticalmass\SocialNetwork\FeedsApi\Dto\Network;
use App\Criticalmass\SocialNetwork\FeedsApi\FeedsApiClientInterface;
use App\Criticalmass\SocialNetwork\FeedsApi\FeedsNetworkManager;
use App\Criticalmass\SocialNetwork\NetworkManager\NetworkManagerInterface;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[TestDox('FeedsNetworkManager')]
class FeedsNetworkManagerTest extends TestCase
{
    private FeedsApiClientInterface&MockObject $feedsApiClient;
    private CacheInterface&MockObject $cache;
    private FeedsNetworkManager $manager;

    protected function setUp(): void
    {
        $this->feedsApiClient = $this->createMock(FeedsApiClientInterface::class);
        $this->cache = $this->createMock(CacheInterface::class);
        $this->manager = new FeedsNetworkManager($this->feedsApiClient, $this->cache);
    }

    private function createNetworks(): array
    {
        return [
            Network::fromApiResponse([
                'id' => 108,
                'identifier' => 'twitter',
                'name' => 'Twitter',
                'icon' => 'fab fa-twitter',
                'backgroundColor' => 'rgb(29, 161, 242)',
                'textColor' => 'white',
                'profileUrlPattern' => '#^https?://(www\.)?twitter\.com/[A-Za-z0-9_]+/?$#i',
            ]),
            Network::fromApiResponse([
                'id' => 109,
                'identifier' => 'mastodon',
                'name' => 'Mastodon',
                'icon' => 'fab fa-mastodon',
                'backgroundColor' => 'rgb(96, 94, 239)',
                'textColor' => 'white',
                'profileUrlPattern' => '#^https?://[A-Za-z0-9.\-]+/@[A-Za-z0-9_]+/?$#i',
            ]),
        ];
    }

    private function setupCacheToCallCallback(): void
    {
        $networks = $this->createNetworks();

        $this->cache->method('get')->willReturnCallback(function (string $key, callable $callback) use ($networks): array {
            $item = $this->createMock(ItemInterface::class);
            $item->method('expiresAfter')->willReturn($item);

            $this->feedsApiClient->method('getNetworks')->willReturn($networks);

            return $callback($item);
        });
    }

    #[TestDox('implements NetworkManagerInterface')]
    public function testImplementsInterface(): void
    {
        $this->assertInstanceOf(NetworkManagerInterface::class, $this->manager);
    }

    #[TestDox('returns network list indexed by identifier')]
    public function testGetNetworkList(): void
    {
        $this->setupCacheToCallCallback();

        $list = $this->manager->getNetworkList();

        $this->assertArrayHasKey('twitter', $list);
        $this->assertArrayHasKey('mastodon', $list);
        $this->assertCount(2, $list);
    }

    #[TestDox('hasNetwork returns true for existing networks')]
    public function testHasNetworkTrue(): void
    {
        $this->setupCacheToCallCallback();

        $this->assertTrue($this->manager->hasNetwork('twitter'));
        $this->assertTrue($this->manager->hasNetwork('mastodon'));
    }

    #[TestDox('hasNetwork returns false for unknown networks')]
    public function testHasNetworkFalse(): void
    {
        $this->setupCacheToCallCallback();

        $this->assertFalse($this->manager->hasNetwork('tiktok'));
    }

    #[TestDox('getNetwork returns correct network by identifier')]
    public function testGetNetwork(): void
    {
        $this->setupCacheToCallCallback();

        $network = $this->manager->getNetwork('twitter');

        $this->assertEquals('twitter', $network->getIdentifier());
        $this->assertEquals('Twitter', $network->getName());
    }

    #[TestDox('getNetwork throws exception for unknown identifier')]
    public function testGetNetworkThrowsForUnknown(): void
    {
        $this->setupCacheToCallCallback();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Network "tiktok" not found');

        $this->manager->getNetwork('tiktok');
    }

    #[TestDox('caches network list and does not call API twice')]
    public function testCachesNetworkList(): void
    {
        $networks = $this->createNetworks();
        $indexed = [];
        foreach ($networks as $n) {
            $indexed[$n->getIdentifier()] = $n;
        }

        $this->cache->expects($this->once())->method('get')->willReturn($indexed);

        $this->manager->getNetworkList();
        $this->manager->getNetworkList();
    }
}
