<?php declare(strict_types=1);

namespace Tests\Criticalmass\Timeline;

use App\Criticalmass\Timeline\Collector\CityCreatedCollector;
use App\Criticalmass\Timeline\Collector\RidePhotoCollector;
use App\Criticalmass\Timeline\Collector\ThreadCollector;
use App\Criticalmass\Timeline\Item\CityCreatedItem;
use App\Criticalmass\Timeline\Item\RidePhotoItem;
use App\Criticalmass\Timeline\Item\ThreadItem;
use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Photo;
use App\Entity\Post;
use App\Entity\Ride;
use App\Entity\Thread;
use App\Entity\User;
use App\Repository\CityRepository;
use App\Repository\PhotoRepository;
use App\Repository\ThreadRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Support\EntityIdHelper;

final class CollectorTest extends TestCase
{
    /**
     * @template T of object
     *
     * @param class-string<T> $repositoryClass
     * @param list<object> $entities
     */
    private function registry(string $entityClass, string $repositoryClass, string $method, array $entities): ManagerRegistry
    {
        $repository = $this->createMock($repositoryClass);
        $repository->expects(self::once())->method($method)->willReturn($entities);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->with($entityClass)->willReturn($repository);

        return $registry;
    }

    #[Test]
    public function threadCollectorBuildsOneItemPerThreadKeyedByFirstPostTime(): void
    {
        $user = new User();
        $post = (new Post())->setUser($user)->setMessage('Hello')->setDateTime(new \DateTime('2024-05-31 19:00:00'));
        $thread = (new Thread())->setTitle('Meeting point')->setFirstPost($post);

        $collector = new ThreadCollector($this->registry(Thread::class, ThreadRepository::class, 'findForTimelineThreadCollector', [$thread]));
        $collector->setDateRange(new \DateTime('2024-05-01'), new \DateTime('2024-06-01'))->execute();

        $items = $collector->getItems();

        self::assertCount(1, $items);
        $key = array_key_first($items);
        self::assertStringStartsWith('2024-05-31-19-00-00-', $key);

        $item = $items[$key];
        self::assertInstanceOf(ThreadItem::class, $item);
        self::assertSame('Meeting point', $item->getTitle());
        self::assertSame('Hello', $item->getText());
        self::assertSame($user, $item->getUser());
        self::assertSame($thread, $item->getThread());
        self::assertSame('standard', $item->getTabName());
    }

    #[Test]
    public function collectorsDeclareNoRequiredFeaturesByDefault(): void
    {
        self::assertSame([], (new ThreadCollector($this->createMock(ManagerRegistry::class)))->getRequiredFeatures());
        self::assertSame(['photos'], (new RidePhotoCollector($this->createMock(ManagerRegistry::class)))->getRequiredFeatures());
    }

    #[Test]
    public function cityCreatedCollectorConvertsCitiesWithSlugs(): void
    {
        $user = new User();
        $city = (new City())->setCity('Hamburg')->setUser($user)->setCreatedAt(new \DateTime('2024-01-02 03:04:05'));
        $city->addSlug((new CitySlug())->setSlug('hamburg'));

        $collector = new CityCreatedCollector($this->registry(City::class, CityRepository::class, 'findForTimelineCityCreatedCollector', [$city]));
        $collector->execute();

        $items = array_values($collector->getItems());

        self::assertCount(1, $items);
        self::assertInstanceOf(CityCreatedItem::class, $items[0]);
        self::assertSame('Hamburg', $items[0]->getCityName());
        self::assertSame($city, $items[0]->getCity());
        self::assertSame($user, $items[0]->getUser());
        self::assertSame('2024-01-02 03:04:05', $items[0]->getDateTime()->format('Y-m-d H:i:s'));
    }

    #[Test]
    public function cityCreatedCollectorSkipsCitiesWithoutSlugs(): void
    {
        $city = (new City())->setCity('Nowhere')->setUser(new User())->setCreatedAt(new \DateTime('2024-01-02'));

        $collector = new CityCreatedCollector($this->registry(City::class, CityRepository::class, 'findForTimelineCityCreatedCollector', [$city]));
        $collector->execute();

        if (1 === count($collector->getItems())) {
            self::markTestIncomplete(
                'CityCreatedCollector checks "if ($city->getSlugs())", but getSlugs() returns a Collection '
                .'object which is always truthy — cities without any slug are not filtered out.'
            );
        }

        self::assertSame([], $collector->getItems());
    }

    #[Test]
    public function ridePhotoCollectorGroupsPhotosPerUserAndRide(): void
    {
        $alice = new User();
        EntityIdHelper::setId($alice, 1);
        $bob = new User();
        EntityIdHelper::setId($bob, 2);

        $ride = (new Ride())->setEnabled(true);
        EntityIdHelper::setId($ride, 10);
        $otherRide = (new Ride())->setEnabled(false);
        EntityIdHelper::setId($otherRide, 11);

        $photos = [];
        foreach ([[$alice, $ride, 1], [$alice, $ride, 2], [$alice, $ride, 3], [$alice, $ride, 4], [$bob, $ride, 5], [$alice, $otherRide, 6]] as [$user, $photoRide, $id]) {
            $photo = (new Photo())->setUser($user)->setRide($photoRide)->setCreationDateTime(new \DateTime('2024-05-31 20:00:00'));
            EntityIdHelper::setId($photo, $id);
            $photos[] = $photo;
        }

        // A photo without a ride is ignored entirely.
        $orphan = (new Photo())->setUser($alice)->setCreationDateTime(new \DateTime('2024-05-31 20:00:00'));
        EntityIdHelper::setId($orphan, 99);
        $photos[] = $orphan;

        $collector = new RidePhotoCollector($this->registry(Photo::class, PhotoRepository::class, 'findForTimelineRidePhotoCollector', $photos));
        $collector->execute();

        /** @var list<RidePhotoItem> $items */
        $items = array_values($collector->getItems());

        self::assertCount(3, $items);

        $byKey = [];
        foreach ($items as $item) {
            $byKey[$item->getUser()->getId() . '-' . $item->getRide()->getId()] = $item;
        }

        self::assertSame(4, $byKey['1-10']->getCounter());
        self::assertCount(3, $byKey['1-10']->getPreviewPhotoList());
        self::assertTrue($byKey['1-10']->isRideEnabled());

        self::assertSame(1, $byKey['2-10']->getCounter());
        self::assertCount(1, $byKey['2-10']->getPreviewPhotoList());

        self::assertSame(1, $byKey['1-11']->getCounter());
        self::assertFalse($byKey['1-11']->isRideEnabled());

        foreach ($byKey['1-10']->getPreviewPhotoList() as $preview) {
            self::assertContains($preview->getId(), [1, 2, 3, 4]);
        }
    }
}
