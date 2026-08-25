<?php declare(strict_types=1);

namespace Tests\Entity;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Ride;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Support\EntityIdHelper;

final class RideTest extends TestCase
{
    private function city(int $id, string $title): City
    {
        $city = (new City())->setId($id)->setCity($title);
        $city->setTitle($title);
        $city->setMainSlug((new CitySlug())->setSlug(strtolower($title))->setCity($city));

        return $city;
    }

    #[Test]
    public function hasSlugOnlyWhenSlugIsSet(): void
    {
        self::assertFalse((new Ride())->hasSlug());
        self::assertTrue((new Ride())->setSlug('kidical-mass')->hasSlug());
        self::assertFalse((new Ride())->setSlug('x')->setSlug(null)->hasSlug());
    }

    #[Test]
    public function sameRideMeansSameCityAndSameDay(): void
    {
        $hamburg = $this->city(1, 'Hamburg');
        $berlin = $this->city(2, 'Berlin');

        $ride = (new Ride())->setCity($hamburg)->setDateTime(new \DateTime('2024-05-31 19:00:00'));

        self::assertTrue($ride->isSameRide((new Ride())->setCity($hamburg)->setDateTime(new \DateTime('2024-05-31 08:00:00'))));
        self::assertFalse($ride->isSameRide((new Ride())->setCity($hamburg)->setDateTime(new \DateTime('2024-06-01 19:00:00'))));
        self::assertFalse($ride->isSameRide((new Ride())->setCity($berlin)->setDateTime(new \DateTime('2024-05-31 19:00:00'))));
    }

    #[Test]
    public function isEqualComparesIds(): void
    {
        $a = new Ride();
        EntityIdHelper::setId($a, 5);
        $b = new Ride();
        EntityIdHelper::setId($b, 5);
        $c = new Ride();
        EntityIdHelper::setId($c, 6);

        self::assertTrue($a->isEqual($b));
        self::assertTrue($a->equals($b));
        self::assertFalse($a->isEqual($c));
    }

    #[Test]
    public function stringRepresentationCombinesCityAndDate(): void
    {
        $ride = (new Ride())->setCity($this->city(1, 'Hamburg'))->setDateTime(new \DateTime('2024-05-31 19:00:00'));

        self::assertSame('Hamburg - 2024-05-31', (string) $ride);
        self::assertSame('2024-05-31', (string) (new Ride())->setDateTime(new \DateTime('2024-05-31')));
        self::assertSame('Hamburg - unknown', (string) (new Ride())->setCity($this->city(1, 'Hamburg'))->setDateTime(null));
    }

    #[Test]
    public function coordAssignsLatitudeAndLongitude(): void
    {
        $ride = (new Ride())->setCoord(new \App\Criticalmass\Geo\Coord\Coord(53.55, 9.99));

        self::assertSame(53.55, $ride->getLatitude());
        self::assertSame(9.99, $ride->getLongitude());
    }

    #[Test]
    public function newRideIsEnabledAndDatedNow(): void
    {
        $ride = new Ride();

        self::assertTrue($ride->isEnabled());
        self::assertNull($ride->getDisabledReason());
        self::assertEqualsWithDelta(time(), $ride->getDateTime()->getTimestamp(), 5);
        self::assertEqualsWithDelta(time(), $ride->getCreatedAt()->getTimestamp(), 5);
    }
}
