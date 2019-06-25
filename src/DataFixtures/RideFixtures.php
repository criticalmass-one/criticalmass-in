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
        $manager->persist($this->createRide('hamburg', new \DateTime('2011-03-25 19:00:00'), 53.5, 10.5));
        $manager->persist($this->createRide('hamburg', new \DateTime('2011-06-24 19:00:00'), 53.5, 10.5));
        $manager->persist($this->createRide('hamburg', new \DateTime('2011-07-29 19:00:00'), 53.5, 10.5));

        $manager->flush();
    }

    protected function createRide(string $citySlug, \DateTime $dateTime, float $latitude, float $longitude): Ride
    {
        $ride = new Ride();
        $ride
            ->setCity($this->getReference(sprintf('city-%s', $citySlug)))
            ->setLatitude($latitude)
            ->setLongitude($longitude)
            ->setTitle(sprintf('Critical Mass %s', $dateTime->format('d.m.Y')))
            ->setDateTime($dateTime);

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
