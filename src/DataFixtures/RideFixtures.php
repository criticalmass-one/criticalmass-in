<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Ride;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class RideFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $this->createOtherRides($manager);
        $this->createSpecialHamburgRides($manager);

        $manager->flush();
    }

    protected function createSpecialHamburgRides(ObjectManager $manager): void
    {
        $manager->persist($this->createRide('hamburg', new \DateTimeImmutable('2011-03-25 19:00:00'), null, 53.5, 10.5));
        $manager->persist($this->createRide('hamburg', new \DateTimeImmutable('2011-06-24 19:00:00'), null, 53.5, 10.5));
        $manager->persist($this->createRide('hamburg', new \DateTimeImmutable('2011-07-29 19:00:00'), null, 53.5, 10.5));

        $manager->persist($this->createRide('hamburg', new \DateTimeImmutable('2050-09-24 19:00:00'), null, 53.5, 10.5));
        $manager->persist($this->createRide('hamburg', new \DateTimeImmutable('2035-06-24 19:00:00'), 'kidical-mass-hamburg-2035', 53.5, 10.5));
    }

    protected function createOtherRides(ObjectManager $manager): void
    {
        $citySlugs = ['hamburg', 'halle', 'berlin', 'mainz', 'london', 'esslingen'];

        $endDateTime = new \DateTime('2029-12-31');
        $interval = new \DateInterval('P1M');

        /** @var string $citySlug */
        foreach ($citySlugs as $citySlug) {
            $dateTime = new \DateTimeImmutable('2015-01-01 19:00:00', new \DateTimeZone('UTC'));

            while ($dateTime < $endDateTime) {
                $ride = $this->createRide($citySlug, $dateTime, null,53.5, 10.5);

                $manager->persist($ride);

                $dateTime = $dateTime->add($interval);
            }
        }
    }

    protected function createRide(string $citySlug, \DateTimeImmutable $dateTime, string $rideSlug = null, float $latitude, float $longitude): Ride
    {
        $rideDateTime = \DateTime::createFromImmutable($dateTime);
        $ride = new Ride();
        $ride
            ->setCity($this->getReference(sprintf('city-%s', $citySlug)))
            ->setTitle(sprintf('Critical Mass %s', $dateTime->format('d.m.Y')))
            ->setDateTime($rideDateTime)
            ->setSlug($rideSlug)
            ->setLatitude($latitude)
            ->setLongitude($longitude);

        $this->setReference(sprintf('ride-%s-%d', $citySlug, $dateTime->format('U')), $ride);

        return $ride;
    }

    public function getDependencies(): array
    {
        return [
            CityFixtures::class,
        ];
    }
}
