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
        $this->createCity($manager, 'Erfurt', 50.978056, 11.029167, 'thueringen');
        $this->createCity($manager, 'Kiel', 54.32321, 10.14019, 'schleswig-holstein');
        $this->createCity($manager, 'Magdeburg', 52.133333, 11.616667, 'sachsen-anhalt');
        $this->createCity($manager, 'Dresden', 51.049259, 13.73836, 'sachsen');
        $this->createCity($manager, 'Saarbrücken', 0.0, 0.0, 'saarland');
        $this->createCity($manager, 'Mainz', 50, 8.271111, 'rheinland-pfalz');
        $this->createCity($manager, 'Düsseldorf', 51.225556, 6.782778, 'nordrhein-westfalen');
        $this->createCity($manager, 'Hannover', 52.374444, 9.738611, 'niedersachsen');
        $this->createCity($manager, 'Schwerin', 53.633333, 11.416667, 'mecklenburg-vorpommern');
        $this->createCity($manager, 'Wiesbaden', 50.0825, 8.24, 'hessen');
        $this->createCity($manager, 'Hamburg', 53.550556, 9.993333, 'hamburg');
        $this->createCity($manager, 'Bremen', 53.075878, 8.807311, 'bremen');
        $this->createCity($manager, 'Potsdam', 52.395833, 13.061389, 'brandenburg');
        $this->createCity($manager, 'Berlin', 52.518611, 13.408333, 'berlin');
        $this->createCity($manager, 'München', 48.137222, 11.575556, 'bayern');
        $this->createCity($manager, 'Stuttgart', 48.775556, 9.182778, 'baden-wuerttemberg');

        $manager->persist($this->createCity($manager, 'London', 51.50939, -0.11832, null, 'Europe/London'));

        $manager->flush();
    }

    protected function createCity(ObjectManager $manager, string $cityName, float $latitude, float $longitude, string $regionSlug = null, string $timezone = 'Europe/Berlin'): City
    {
        $city = new City();
        $city
            ->setUser($this->getReference('user-maltehuebner'))
            ->setTitle(sprintf('Critical Mass %s', $cityName))
            ->setCity($cityName)
            ->setTimezone($timezone)
            ->setLatitude($latitude)
            ->setLongitude($longitude);

        if ($regionSlug) {
            $city->setRegion($this->getReference(sprintf('region-%s', $regionSlug)));
        }

        $citySlugs = CitySlugHandler::createSlugsForCity($city);

        $this->setReference(sprintf('city-%s', strtolower($cityName)), $city);

        $manager->persist($city);

        return $city;
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            RegionFixtures::class,
        ];
    }
}
