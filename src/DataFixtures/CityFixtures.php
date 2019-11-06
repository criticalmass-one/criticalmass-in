<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Criticalmass\CitySlug\Handler\CitySlugHandler;
use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CityFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $manager->persist($this->createCity('Hamburg', 53.550556, 9.993333));
        $manager->persist($this->createCity('Halle', 51.482778, 11.97));
        $manager->persist($this->createCity('Berlin', 52.518611, 13.408333));
        $manager->persist($this->createCity('Mainz', 50, 8.271111));
        $manager->persist($this->createCity('London', 51.50939, -0.11832, 'Europe/London'));
        $manager->persist($this->createCity('Esslingen', 48.740556, 9.310833));

        $manager->flush();
    }

    protected function createCity(string $cityName, float $latitude, float $longitude, string $timezone = 'Europe/Berlin'): City
    {
        $city = new City();
        $city
            ->setUser($this->getReference('user-maltehuebner'))
            ->setTitle(sprintf('Critical Mass %s', $cityName))
            ->setCity($cityName)
            ->setTimezone($timezone)
            ->setLatitude($latitude)
            ->setLongitude($longitude);

        $citySlugs = CitySlugHandler::createSlugsForCity($city);

        $this->setReference(sprintf('city-%s', strtolower($cityName)), $city);

        return $city;
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
