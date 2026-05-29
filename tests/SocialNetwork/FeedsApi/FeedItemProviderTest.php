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
    private FeedsApiClientInterface&MockObject $feedsApiClient;
    private CacheInterface&MockObject $cache;
    private MockObject $profileRepository;
    private FeedItemProvider $provider;

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
        );
    }

    private function setupCachePassthrough(): void
    {
        $this->cache->method('get')->willReturnCallback(function (string $key, callable $callback) {
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

    #[TestDox('fetches feed items for each profile and sorts by date descending')]
    public function testFetchesAndSortsByDate(): void
    {
        $this->setupCachePassthrough();
        $this->profileRepository->method('findByCity')->willReturn([
            $this->createProfile(10),
            $this->createProfile(20),
        ]);

        $this->feedsApiClient->method('getItems')
            ->willReturnCallback(function (int $profileId) {
                if ($profileId === 10) {
                    return [$this->createFeedItem(1, '2026-03-15T10:00:00+01:00')];
                }

                return [$this->createFeedItem(2, '2026-03-15T18:00:00+01:00')];
            });

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
            ->willReturn([$this->createFeedItem(1, '2026-03-15T10:00:00+01:00')]);

        $items = $this->provider->getFeedItemsForCity($this->createCity(1));

        $this->assertCount(1, $items);
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
                since: $since,
                until: $until,
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
}
