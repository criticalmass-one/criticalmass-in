<?php declare(strict_types=1);

namespace Tests\Criticalmass\Timeline;

use App\Criticalmass\Timeline\Collector\TimelineCollectorInterface;
use App\Criticalmass\Timeline\Item\CityCreatedItem;
use App\Criticalmass\Timeline\Item\ItemInterface;
use App\Criticalmass\Timeline\Item\RidePhotoItem;
use App\Criticalmass\Timeline\Item\ThreadItem;
use App\Criticalmass\Timeline\Timeline;
use Doctrine\Persistence\ManagerRegistry;
use Flagception\Manager\FeatureManagerInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

final class TimelineTest extends TestCase
{
    /** @var list<string> */
    private array $activeFeatures = [];

    private function timeline(): Timeline
    {
        $twig = new Environment(new ArrayLoader([
            'Timeline/Items/thread.html.twig' => 'thread:{{ item.title }}',
            'Timeline/Items/cityCreated.html.twig' => 'city:{{ item.cityName }}',
            'Timeline/Items/ridePhoto.html.twig' => 'photos:{{ item.counter }}',
        ]));

        $featureManager = $this->createMock(FeatureManagerInterface::class);
        $featureManager->method('isActive')->willReturnCallback(fn (string $feature): bool => in_array($feature, $this->activeFeatures, true));

        $timeline = new Timeline($this->createMock(ManagerRegistry::class), $twig, $featureManager);
        $timeline->setDateRange(new \DateTime('-1 month'), new \DateTime());

        return $timeline;
    }

    /**
     * @param array<string, ItemInterface> $items
     * @param list<string> $requiredFeatures
     */
    private function collector(array $items, array $requiredFeatures = []): MockObject&TimelineCollectorInterface
    {
        $collector = $this->createMock(TimelineCollectorInterface::class);
        $collector->method('setDateRange')->willReturnSelf();
        $collector->method('execute')->willReturnSelf();
        $collector->method('getItems')->willReturn($items);
        $collector->method('getRequiredFeatures')->willReturn($requiredFeatures);

        return $collector;
    }

    private function threadItem(string $title, string $dateTime): ThreadItem
    {
        $item = (new ThreadItem())->setTitle($title);
        $item->setDateTime(new \DateTime($dateTime));

        return $item;
    }

    #[Test]
    public function itemsAreRenderedNewestFirstAcrossCollectors(): void
    {
        $older = $this->threadItem('older', '-3 days');
        $newer = $this->threadItem('newer', '-1 day');
        $city = (new CityCreatedItem())->setCityName('Hamburg')->setDateTime(new \DateTime('-2 days'));

        $timeline = $this->timeline()
            ->addCollector($this->collector(['2020-01-01-00-00-00-a' => $older, '2020-01-03-00-00-00-c' => $newer]))
            ->addCollector($this->collector(['2020-01-02-00-00-00-b' => $city]))
            ->execute();

        self::assertSame(['standard' => ['thread:newer', 'city:Hamburg', 'thread:older']], $timeline->getTimelineContentList());
    }

    #[Test]
    public function collectorsWithInactiveFeaturesAreIgnored(): void
    {
        $this->activeFeatures = ['threads'];

        $shown = $this->collector(['k1' => $this->threadItem('shown', '-1 day')], ['threads']);
        $hidden = $this->collector(['k2' => (new RidePhotoItem())->setCounter(3)->setDateTime(new \DateTime('-1 day'))], ['photos']);
        $hidden->expects(self::never())->method('execute');

        $timeline = $this->timeline()->addCollector($shown)->addCollector($hidden)->execute();

        self::assertSame(['standard' => ['thread:shown']], $timeline->getTimelineContentList());
    }

    #[Test]
    public function collectorRequiringSeveralFeaturesNeedsAllOfThem(): void
    {
        $this->activeFeatures = ['threads'];

        $collector = $this->collector(['k1' => $this->threadItem('x', '-1 day')], ['threads', 'photos']);
        $collector->expects(self::never())->method('execute');

        self::assertSame([], $this->timeline()->addCollector($collector)->execute()->getTimelineContentList());
    }

    #[Test]
    public function itemsOlderThanThreeMonthsAreDropped(): void
    {
        $items = [
            'b' => $this->threadItem('recent', '-2 months'),
            'a' => $this->threadItem('ancient', '-4 months'),
        ];

        $timeline = $this->timeline()->addCollector($this->collector($items))->execute();

        self::assertSame(['standard' => ['thread:recent']], $timeline->getTimelineContentList());
    }

    #[Test]
    public function atMostHundredItemsPerTabSurvive(): void
    {
        $items = [];
        for ($i = 0; $i < 120; ++$i) {
            $items[sprintf('item-%03d', $i)] = $this->threadItem((string) $i, '-1 day');
        }

        $timeline = $this->timeline()->addCollector($this->collector($items))->execute();

        $rendered = $timeline->getTimelineContentList()['standard'];

        self::assertCount(Timeline::MAX_ITEMS, $rendered);
        self::assertSame('thread:119', $rendered[0]);
        self::assertSame('thread:20', $rendered[99]);
    }

    #[Test]
    public function emptyTimelineHasNoContent(): void
    {
        self::assertSame([], $this->timeline()->execute()->getTimelineContentList());
    }
}
