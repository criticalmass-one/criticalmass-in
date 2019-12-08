<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Ride;
use Caldera\GeoBasic\Coord\Coord;
use Caldera\GeoBasic\Coord\CoordInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class RideFixtures extends Fixture implements DependentFixtureInterface
{
    protected $rideLocationCoords = [];

    public function load(ObjectManager $manager): void
    {
        $this->initRideLocationCoords();

        $this->createOtherRides($manager);
        $this->createSpecialHamburgRides($manager);

        $manager->flush();
    }

    protected function createSpecialHamburgRides(ObjectManager $manager): void
    {
        $hamburgLocationCoord = $this->rideLocationCoords['hamburg'];

        $manager->persist($this->createRide('hamburg', new \DateTimeImmutable('2011-03-25 19:00:00'), null, $hamburgLocationCoord->getLatitude(), $hamburgLocationCoord->getLongitude()));
        $manager->persist($this->createRide('hamburg', new \DateTimeImmutable('2011-06-24 19:00:00'), null, $hamburgLocationCoord->getLatitude(), $hamburgLocationCoord->getLongitude()));
        $manager->persist($this->createRide('hamburg', new \DateTimeImmutable('2011-07-29 19:00:00'), null, $hamburgLocationCoord->getLatitude(), $hamburgLocationCoord->getLongitude()));

        $manager->persist($this->createRide('hamburg', new \DateTimeImmutable('2050-09-24 19:00:00'), null, $hamburgLocationCoord->getLatitude(), $hamburgLocationCoord->getLongitude()));
        $manager->persist($this->createRide('hamburg', new \DateTimeImmutable('2035-06-24 19:00:00'), 'kidical-mass-hamburg-2035', $hamburgLocationCoord->getLatitude(), $hamburgLocationCoord->getLongitude()));
    }

    protected function createOtherRides(ObjectManager $manager): void
    {
        $citySlugs = ['hamburg', 'berlin', 'mainz', 'london', 'wiesbaden'];

        $endDateTime = new \DateTime('2029-12-31');
        $interval = new \DateInterval('P1M');

        /** @var string $citySlug */
        foreach ($citySlugs as $citySlug) {
            $dateTime = new \DateTimeImmutable('2015-01-01 19:00:00', new \DateTimeZone('UTC'));

            while ($dateTime < $endDateTime) {
                /** @var CoordInterface $locationCoord */
                $locationCoord = $this->rideLocationCoords[$citySlug];

                $ride = $this->createRide($citySlug, $dateTime, null, $locationCoord->getLatitude(), $locationCoord->getLongitude());

                $manager->persist($ride);

                $dateTime = $dateTime->add($interval);
            }
        }
    }

    protected function createRide(string $citySlug, \DateTimeImmutable $dateTime, string $rideSlug = null, float $latitude, float $longitude): Ride
    {
        $rideDateTime = \DateTime::createFromImmutable($dateTime);
        $city = $this->getReference(sprintf('city-%s', $citySlug));

        $ride = new Ride();
        $ride
            ->setCity($city)
            ->setTitle(sprintf('Critical Mass %s %s', $city->getCity(), $dateTime->format('d.m.Y')))
            ->setDateTime($rideDateTime)
            ->setSlug($rideSlug)
            ->setLatitude($latitude)
            ->setLongitude($longitude);

        $this->setReference(sprintf('ride-%s-%s', $citySlug, $dateTime->format('Y-m-d')), $ride);

        return $ride;
    }

    public function getDependencies(): array
    {
        return [
            CityFixtures::class,
        ];
    }

    protected function initRideLocationCoords(): void
    {
        $this->rideLocationCoords = [
            'hamburg' => new Coord(53.566676, 9.984711),
            'berlin' => new Coord(52.500472, 13.423083),
            'mainz' => new Coord(50.001452, 8.276696),
            'london' => new Coord(51.507620, -0.114708),
            'wiesbaden' => new Coord(50.0825, 8.24),
        ];
    }
}
