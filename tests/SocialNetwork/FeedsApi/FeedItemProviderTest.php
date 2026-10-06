<?php declare(strict_types=1);

namespace Tests\SocialNetwork\FeedsApi;

use App\Criticalmass\SocialNetwork\FeedsApi\Dto\FeedItem;
use App\Criticalmass\SocialNetwork\FeedsApi\FeedItemProvider;
use App\Criticalmass\SocialNetwork\FeedsApi\FeedsApiClientInterface;
use App\Entity\City;
use App\Entity\SocialNetworkProfile;
use App\Repository\SocialNetworkProfileRepository;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[TestDox('FeedItemProvider')]
class FeedItemProviderTest extends TestCase
{
    private const int CACHE_TTL = 5400;

    private FeedsApiClientInterface&MockObject $feedsApiClient;
    private CacheInterface&MockObject $cache;
    private MockObject $profileRepository;
    private FeedItemProvider $provider;

    /** @var list<string> */
    private array $cacheKeys = [];

    /** @var list<float|null> */
    private array $cacheBetas = [];

    protected function setUp(): void
    {
        $this->feedsApiClient = $this->createMock(FeedsApiClientInterface::class);
        $this->cache = $this->createMock(CacheInterface::class);

        $this->profileRepository = $this->getMockBuilder(SocialNetworkProfileRepository::class)
            ->disableOriginalConstructor()
            ->addMethods(['findByCity'])
            ->getMock();

        $this->provider = new FeedItemProvider(
            $this->feedsApiClient,
            $this->profileRepository,
            $this->cache,
            self::CACHE_TTL,
        );
    }

    private function setupCachePassthrough(): void
    {
        $this->cache->method('get')->willReturnCallback(function (string $key, callable $callback, ?float $beta = null) {
            $this->cacheKeys[] = $key;
            $this->cacheBetas[] = $beta;

            $item = $this->createMock(ItemInterface::class);
            $item->method('expiresAfter')->willReturn($item);

            return $callback($item);
        });
    }

    private function createCity(int $id): City&MockObject
    {
        $city = $this->createMock(City::class);
        $city->method('getId')->willReturn($id);

        return $city;
    }

    private function createProfile(?int $feedsProfileId): SocialNetworkProfile&MockObject
    {
        $profile = $this->createMock(SocialNetworkProfile::class);
        $profile->method('getFeedsProfileId')->willReturn($feedsProfileId);

        return $profile;
    }

    private function createFeedItem(int $id, string $dateTime): FeedItem
    {
        return FeedItem::fromApiResponse([
            'id' => $id,
            'uniqueIdentifier' => 'item-' . $id,
            'text' => 'Post ' . $id,
            'dateTime' => $dateTime,
            'createdAt' => $dateTime,
            'profile' => ['id' => 1],
        ]);
    }

    #[TestDox('returns empty array when city has no profiles with feedsProfileId')]
    public function testReturnsEmptyForCityWithoutFeedsProfiles(): void
    {
        $this->setupCachePassthrough();
        $this->profileRepository->method('findByCity')->willReturn([$this->createProfile(null)]);

        $items = $this->provider->getFeedItemsForCity($this->createCity(1));

        $this->assertEmpty($items);
    }

    #[TestDox('asks for all of the city profiles in a single request')]
    public function testFetchesAllProfilesAtOnce(): void
    {
        $this->setupCachePassthrough();
        $this->profileRepository->method('findByCity')->willReturn([
            $this->createProfile(10),
            $this->createProfile(20),
        ]);

        $this->feedsApiClient->expects($this->once())
            ->method('getItems')
            ->with([10, 20], 1, 'desc')
            ->willReturn([
                $this->createFeedItem(2, '2026-03-15T18:00:00+01:00'),
                $this->createFeedItem(1, '2026-03-15T10:00:00+01:00'),
            ]);

        $items = $this->provider->getFeedItemsForCity($this->createCity(1));

        $this->assertCount(2, $items);
        $this->assertEquals(2, $items[0]->getId());
        $this->assertEquals(1, $items[1]->getId());
    }

    #[TestDox('skips profiles without feedsProfileId')]
    public function testSkipsProfilesWithoutFeedsId(): void
    {
        $this->setupCachePassthrough();
        $this->profileRepository->method('findByCity')->willReturn([
            $this->createProfile(null),
            $this->createProfile(10),
        ]);

        $this->feedsApiClient->expects($this->once())
            ->method('getItems')
            ->with([10], 1, 'desc')
            ->willReturn([$this->createFeedItem(1, '2026-03-15T10:00:00+01:00')]);

        $items = $this->provider->getFeedItemsForCity($this->createCity(1));

        $this->assertCount(1, $items);
    }

    #[TestDox('returns empty array when the city feed API call fails')]
    public function testReturnsEmptyWhenCityApiFails(): void
    {
        $this->setupCachePassthrough();
        $this->profileRepository->method('findByCity')->willReturn([$this->createProfile(10)]);

        $this->feedsApiClient->method('getItems')
            ->willThrowException(new \RuntimeException('Feeds API returned status 401'));

        $items = $this->provider->getFeedItemsForCity($this->createCity(1));

        $this->assertSame([], $items);
    }

    #[TestDox('returns empty array when the timeline API call fails')]
    public function testReturnsEmptyWhenTimelineApiFails(): void
    {
        $this->setupCachePassthrough();

        $this->feedsApiClient->method('getTimelineItems')
            ->willThrowException(new \RuntimeException('Feeds API returned status 500'));

        $items = $this->provider->getTimelineItems();

        $this->assertSame([], $items);
    }

    #[TestDox('getTimelineItems passes date range to API client')]
    public function testGetTimelineItemsWithDateRange(): void
    {
        $this->setupCachePassthrough();

        $since = new \DateTime('2026-03-01');
        $until = new \DateTime('2026-03-15');

        $this->feedsApiClient->expects($this->once())
            ->method('getTimelineItems')
            ->with(
                limit: null,
                // Day-aligned by the provider: the 15th belongs to the window.
                since: new \DateTimeImmutable('2026-03-01 00:00:00'),
                until: new \DateTimeImmutable('2026-03-16 00:00:00'),
            )
            ->willReturn([]);

        $this->provider->getTimelineItems(since: $since, until: $until);
    }

    #[TestDox('getTimelineItems passes limit to API client')]
    public function testGetTimelineItemsWithLimit(): void
    {
        $this->setupCachePassthrough();

        $this->feedsApiClient->expects($this->once())
            ->method('getTimelineItems')
            ->with(
                limit: 50,
                since: null,
                until: null,
            )
            ->willReturn([]);

        $this->provider->getTimelineItems(limit: 50);
    }

    #[TestDox('caches feed items for city')]
    public function testCachesFeedItemsForCity(): void
    {
        $this->profileRepository->method('findByCity')->willReturn([
            $this->createProfile(10),
        ]);

        $this->cache->expects($this->once())
            ->method('get')
            ->with('feeds_city_5_page_1', $this->anything())
            ->willReturn([$this->createFeedItem(1, '2026-03-15T10:00:00+01:00')]);

        $items = $this->provider->getFeedItemsForCity($this->createCity(5), 1);

        $this->assertCount(1, $items);
    }

    #[TestDox('gives every timeline window a key of whole days, so it does not rotate hourly')]
    public function testTimelineKeyIsDayAligned(): void
    {
        $this->setupCachePassthrough();
        $this->feedsApiClient->method('getTimelineItems')->willReturn([]);

        $this->provider->getTimelineItems(
            new \DateTimeImmutable('2026-03-01 07:13:00'),
            new \DateTimeImmutable('2026-03-31 19:45:00'),
        );

        $this->provider->getTimelineItems(
            new \DateTimeImmutable('2026-03-01 22:58:00'),
            new \DateTimeImmutable('2026-03-31 02:04:00'),
        );

        // Same day, same entry — the hour the visitor arrives must not matter.
        $this->assertSame(['feeds_timeline_2026-03-01_2026-04-01_0'], array_unique($this->cacheKeys));
    }

    #[TestDox('queries the whole of the last day of the window')]
    public function testTimelineWindowCoversItsLastDay(): void
    {
        $this->setupCachePassthrough();

        $this->feedsApiClient->expects($this->once())
            ->method('getTimelineItems')
            ->with(
                limit: null,
                since: new \DateTimeImmutable('2026-03-01 00:00:00'),
                until: new \DateTimeImmutable('2026-04-01 00:00:00'),
            )
            ->willReturn([]);

        $this->provider->getTimelineItems(
            new \DateTimeImmutable('2026-03-01 07:13:00'),
            new \DateTimeImmutable('2026-03-31 19:45:00'),
        );
    }

    #[TestDox('forces a recomputation when asked to refresh')]
    public function testRefreshBypassesTheCachedValue(): void
    {
        $this->setupCachePassthrough();
        $this->profileRepository->method('findByCity')->willReturn([$this->createProfile(10)]);
        $this->feedsApiClient->method('getItems')->willReturn([]);
        $this->feedsApiClient->method('getTimelineItems')->willReturn([]);

        $this->provider->getFeedItemsForCity($this->createCity(1));
        $this->provider->getFeedItemsForCity($this->createCity(1), refresh: true);
        $this->provider->getTimelineItems(refresh: true);

        $this->assertSame([null, INF, INF], $this->cacheBetas);
    }
}
