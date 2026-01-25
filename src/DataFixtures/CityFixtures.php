<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Region;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends Fixture implements DependentFixtureInterface
{
    public const HAMBURG_REFERENCE = 'city-hamburg';
    public const BERLIN_REFERENCE = 'city-berlin';
    public const MUNICH_REFERENCE = 'city-munich';
    public const KIEL_REFERENCE = 'city-kiel';

    public function load(ObjectManager $manager): void
    {
        /** @var Region $hamburgRegion */
        $hamburgRegion = $this->getReference(RegionFixtures::HAMBURG_REFERENCE, Region::class);
        /** @var Region $berlinRegion */
        $berlinRegion = $this->getReference(RegionFixtures::BERLIN_REFERENCE, Region::class);
        /** @var Region $bayernRegion */
        $bayernRegion = $this->getReference(RegionFixtures::BAYERN_REFERENCE, Region::class);
        /** @var Region $schleswigHolsteinRegion */
        $schleswigHolsteinRegion = $this->getReference(RegionFixtures::SCHLESWIG_HOLSTEIN_REFERENCE, Region::class);
        /** @var User $adminUser */
        $adminUser = $this->getReference(UserFixtures::ADMIN_USER_REFERENCE, User::class);

        $hamburg = $this->createCity(
            'Hamburg',
            'Critical Mass Hamburg',
            'hamburg',
            53.5511,
            9.9937,
            1900000,
            $hamburgRegion,
            $adminUser,
            $manager
        );
        $this->addReference(self::HAMBURG_REFERENCE, $hamburg);

        $berlin = $this->createCity(
            'Berlin',
            'Critical Mass Berlin',
            'berlin',
            52.5200,
            13.4050,
            3600000,
            $berlinRegion,
            $adminUser,
            $manager
        );
        $this->addReference(self::BERLIN_REFERENCE, $berlin);

        $munich = $this->createCity(
            'Munich',
            'Critical Mass Munich',
            'munich',
            48.1351,
            11.5820,
            1500000,
            $bayernRegion,
            $adminUser,
            $manager
        );
        $this->addReference(self::MUNICH_REFERENCE, $munich);

        $kiel = $this->createCity(
            'Kiel',
            'Critical Mass Kiel',
            'kiel',
            54.3233,
            10.1228,
            250000,
            $schleswigHolsteinRegion,
            $adminUser,
            $manager
        );
        $this->addReference(self::KIEL_REFERENCE, $kiel);

        $manager->flush();
    }

    private function createCity(
        string $name,
        string $title,
        string $slug,
        float $latitude,
        float $longitude,
        int $population,
        Region $region,
        User $user,
        ObjectManager $manager
    ): City {
        $citySlug = (new CitySlug())
            ->setSlug($slug);
        $manager->persist($citySlug);

        $city = (new City())
            ->setCity($name)
            ->setTitle($title)
            ->setLatitude($latitude)
            ->setLongitude($longitude)
            ->setCityPopulation($population)
            ->setRegion($region)
            ->setUser($user)
            ->setEnabled(true)
            ->setTimezone('Europe/Berlin');

        $city->addSlug($citySlug);
        $citySlug->setCity($city);

        $manager->persist($city);

        return $city;
    }

    public function getDependencies(): array
    {
        return [
            RegionFixtures::class,
            UserFixtures::class,
        ];
    }
}
