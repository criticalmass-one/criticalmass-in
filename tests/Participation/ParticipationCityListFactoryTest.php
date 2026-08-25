<?php declare(strict_types=1);

namespace Tests\Participation;

use App\Criticalmass\Participation\CityList\ParticipationCityListFactory;
use App\Entity\City;
use App\Entity\Participation;
use App\Entity\Ride;
use App\Entity\User;
use App\Repository\ParticipationRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ParticipationCityListFactoryTest extends TestCase
{
    private function city(int $id, string $name): City
    {
        return (new City())->setId($id)->setCity($name);
    }

    /**
     * @param list<City> $cities one entry per participation
     */
    private function factory(array $cities): ParticipationCityListFactory
    {
        $participations = array_map(
            static fn (City $city): Participation => (new Participation())->setRide((new Ride())->setCity($city)),
            $cities
        );

        $repository = $this->createMock(ParticipationRepository::class);
        $repository->method('findByUser')->willReturn($participations);

        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getRepository')->with(Participation::class)->willReturn($repository);

        return new ParticipationCityListFactory($registry);
    }

    #[Test]
    public function participationsAreCountedPerCity(): void
    {
        $hamburg = $this->city(1, 'Hamburg');
        $berlin = $this->city(2, 'Berlin');

        $list = $this->factory([$hamburg, $berlin, $hamburg])->buildForUser(new User())->getParticipationCityList();

        self::assertSame(2, $list->count());
        self::assertSame(2, $list->getList()[1]->getCounter());
        self::assertSame(1, $list->getList()[2]->getCounter());
    }

    #[Test]
    public function sortOrdersByCounterDescendingThenCityNameAscending(): void
    {
        $hamburg = $this->city(1, 'Hamburg');
        $berlin = $this->city(2, 'Berlin');
        $aachen = $this->city(3, 'Aachen');
        $zwickau = $this->city(4, 'Zwickau');

        $list = $this->factory([$zwickau, $hamburg, $berlin, $aachen, $zwickau, $berlin])
            ->buildForUser(new User())
            ->sort()
            ->getParticipationCityList();

        $names = array_map(static fn ($item): string => $item->getCity()->getCity(), $list->getList());

        self::assertSame(['Berlin', 'Zwickau', 'Aachen', 'Hamburg'], $names);
    }

    #[Test]
    public function userWithoutParticipationsGetsAnEmptyList(): void
    {
        $list = $this->factory([])->buildForUser(new User())->sort()->getParticipationCityList();

        self::assertSame(0, $list->count());
        self::assertSame([], $list->getList());
    }
}
