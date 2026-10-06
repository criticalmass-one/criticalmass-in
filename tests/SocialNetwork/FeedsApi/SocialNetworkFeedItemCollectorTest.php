<?php declare(strict_types=1);

namespace Tests\SocialNetwork\FeedsApi;

use App\Criticalmass\SocialNetwork\FeedsApi\Dto\FeedItem;
use App\Criticalmass\SocialNetwork\FeedsApi\FeedItemProviderInterface;
use App\Criticalmass\Timeline\Collector\SocialNetworkFeedItemCollector;
use App\Criticalmass\Timeline\Collector\TimelineCollectorInterface;
use App\Criticalmass\Timeline\Item\SocialNetworkFeedItemItem;
use App\Repository\SocialNetworkProfileRepository;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[TestDox('SocialNetworkFeedItemCollector')]
class SocialNetworkFeedItemCollectorTest extends TestCase
{
    private FeedItemProviderInterface&MockObject $feedItemProvider;
    private SocialNetworkProfileRepository&MockObject $profileRepository;
    private SocialNetworkFeedItemCollector $collector;

    protected function setUp(): void
    {
        $this->feedItemProvider = $this->createMock(FeedItemProviderInterface::class);
        $this->profileRepository = $this->createMock(SocialNetworkProfileRepository::class);
        $this->collector = new SocialNetworkFeedItemCollector($this->feedItemProvider, $this->profileRepository);
    }

    #[TestDox('implements TimelineCollectorInterface')]
    public function testImplementsInterface(): void
    {
        $this->assertInstanceOf(TimelineCollectorInterface::class, $this->collector);
    }

    #[TestDox('returns empty items before execute')]
    public function testEmptyBeforeExecute(): void
    {
        $this->assertEmpty($this->collector->getItems());
    }

    #[TestDox('collects feed items from provider')]
    public function testCollectsFeedItems(): void
    {
        $start = new \DateTime('2026-03-01');
        $end = new \DateTime('2026-03-15');

        $feedItems = [
            FeedItem::fromApiResponse([
                'id' => 1,
                'uniqueIdentifier' => 'item-1',
                'text' => 'Test post 1',
                'dateTime' => '2026-03-10T18:00:00+01:00',
                'createdAt' => '2026-03-10T19:00:00+01:00',
                'profile' => ['id' => 5],
            ]),
            FeedItem::fromApiResponse([
                'id' => 2,
                'uniqueIdentifier' => 'item-2',
                'text' => 'Test post 2',
                'dateTime' => '2026-03-12T18:00:00+01:00',
                'createdAt' => '2026-03-12T19:00:00+01:00',
                'profile' => ['id' => 5],
            ]),
        ];

        $this->feedItemProvider->expects($this->once())
            ->method('getTimelineItems')
            ->with($start, $end)
            ->willReturn($feedItems);

        $this->profileRepository->method('findNetworkIdentifiersByFeedsProfileIds')->willReturn([]);

        $this->collector->setDateRange($start, $end);
        $result = $this->collector->execute();

        $this->assertSame($this->collector, $result);

        $items = $this->collector->getItems();
        $this->assertCount(2, $items);

        foreach ($items as $item) {
            $this->assertInstanceOf(SocialNetworkFeedItemItem::class, $item);
        }
    }

    #[TestDox('puts every item into the tab of the network it was posted on')]
    public function testTabNameFollowsNetwork(): void
    {
        $feedItems = [
            $this->feedItem(1, 5, '2026-03-10T18:00:00+01:00'),
            $this->feedItem(2, 7, '2026-03-11T18:00:00+01:00'),
            $this->feedItem(3, 9, '2026-03-12T18:00:00+01:00'),
        ];

        $this->feedItemProvider->method('getTimelineItems')->willReturn($feedItems);

        $this->profileRepository->expects($this->once())
            ->method('findNetworkIdentifiersByFeedsProfileIds')
            ->with([5, 7, 9])
            ->willReturn([5 => 'instagram_profile', 7 => 'facebook_page']);

        $this->collector->setDateRange(new \DateTime('2026-03-01'), new \DateTime('2026-03-15'));
        $this->collector->execute();

        $tabNames = array_map(
            static fn(SocialNetworkFeedItemItem $item): string => $item->getTabName(),
            array_values($this->collector->getItems()),
        );

        sort($tabNames);

        // The unknown profile 9 falls back to the general news tab.
        $this->assertSame(['facebook_page', 'instagram_profile', 'standard'], $tabNames);
    }

    #[TestDox('asks the repository only once, for distinct profile ids')]
    public function testProfileIdsAreDeduplicated(): void
    {
        $this->feedItemProvider->method('getTimelineItems')->willReturn([
            $this->feedItem(1, 5, '2026-03-10T18:00:00+01:00'),
            $this->feedItem(2, 5, '2026-03-11T18:00:00+01:00'),
        ]);

        $this->profileRepository->expects($this->once())
            ->method('findNetworkIdentifiersByFeedsProfileIds')
            ->with([5])
            ->willReturn([5 => 'mastodon']);

        $this->collector->setDateRange(new \DateTime('2026-03-01'), new \DateTime('2026-03-15'));
        $this->collector->execute();

        foreach ($this->collector->getItems() as $item) {
            $this->assertSame('mastodon', $item->getTabName());
        }
    }

    private function feedItem(int $id, int $profileId, string $dateTime): FeedItem
    {
        return FeedItem::fromApiResponse([
            'id' => $id,
            'uniqueIdentifier' => sprintf('item-%d', $id),
            'text' => sprintf('Test post %d', $id),
            'dateTime' => $dateTime,
            'createdAt' => $dateTime,
            'profile' => ['id' => $profileId],
        ]);
    }

    #[TestDox('returns empty when provider has no items')]
    public function testEmptyFromProvider(): void
    {
        $this->feedItemProvider->method('getTimelineItems')->willReturn([]);
        $this->profileRepository->method('findNetworkIdentifiersByFeedsProfileIds')->willReturn([]);

        $this->collector->setDateRange(new \DateTime('2026-03-01'), new \DateTime('2026-03-15'));
        $this->collector->execute();

        $this->assertEmpty($this->collector->getItems());
    }

    #[TestDox('has no required features')]
    public function testNoRequiredFeatures(): void
    {
        $this->assertEmpty($this->collector->getRequiredFeatures());
    }
}
