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
        $manager->persist($this->createCity('Hamburg'));
        $manager->persist($this->createCity('Halle'));
        $manager->persist($this->createCity('Berlin'));
        $manager->persist($this->createCity('Mainz'));
        $manager->persist($this->createCity('London', 'Europe/London'));
        $manager->persist($this->createCity('Esslingen'));

        $manager->flush();
    }

    protected function createCity(string $cityName, string $timezone = 'Europe/Berlin'): City
    {
        $city = new City();
        $city
            ->setUser($this->getReference('user-maltehuebner'))
            ->setTitle(sprintf('Critical Mass %s', $cityName))
            ->setCity($cityName)
            ->setTimezone($timezone);

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
