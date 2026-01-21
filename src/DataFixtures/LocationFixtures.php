<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class LocationFixtures extends Fixture implements DependentFixtureInterface
{
    public const HAMBURG_MOORWEIDE_REFERENCE = 'location-hamburg-moorweide';
    public const HAMBURG_JUNGFERNSTIEG_REFERENCE = 'location-hamburg-jungfernstieg';
    public const HAMBURG_RATHAUS_REFERENCE = 'location-hamburg-rathaus';
    public const BERLIN_HEINRICHPLATZ_REFERENCE = 'location-berlin-heinrichplatz';
    public const BERLIN_BRANDENBURGER_TOR_REFERENCE = 'location-berlin-brandenburger-tor';
    public const MUNICH_MARIENPLATZ_REFERENCE = 'location-munich-marienplatz';
    public const KIEL_ASMUS_BREMER_REFERENCE = 'location-kiel-asmus-bremer';

    public function load(ObjectManager $manager): void
    {
        /** @var City $hamburg */
        $hamburg = $this->getReference(CityFixtures::HAMBURG_REFERENCE, City::class);
        /** @var City $berlin */
        $berlin = $this->getReference(CityFixtures::BERLIN_REFERENCE, City::class);
        /** @var City $munich */
        $munich = $this->getReference(CityFixtures::MUNICH_REFERENCE, City::class);
        /** @var City $kiel */
        $kiel = $this->getReference(CityFixtures::KIEL_REFERENCE, City::class);

        $hamburgMoorweide = $this->createLocation(
            $hamburg,
            'moorweide',
            'Moorweide',
            'Traditioneller Startpunkt der Critical Mass Hamburg am Dammtor.',
            53.5611,
            9.9895
        );
        $this->addReference(self::HAMBURG_MOORWEIDE_REFERENCE, $hamburgMoorweide);
        $manager->persist($hamburgMoorweide);

        $hamburgJungfernstieg = $this->createLocation(
            $hamburg,
            'jungfernstieg',
            'Jungfernstieg',
            'Beliebter Zwischenstopp an der Binnenalster.',
            53.5520,
            9.9930
        );
        $this->addReference(self::HAMBURG_JUNGFERNSTIEG_REFERENCE, $hamburgJungfernstieg);
        $manager->persist($hamburgJungfernstieg);

        $hamburgRathaus = $this->createLocation(
            $hamburg,
            'rathaus',
            'Rathausmarkt',
            'Zentraler Platz vor dem Hamburger Rathaus.',
            53.5503,
            9.9928
        );
        $this->addReference(self::HAMBURG_RATHAUS_REFERENCE, $hamburgRathaus);
        $manager->persist($hamburgRathaus);

        $berlinHeinrichplatz = $this->createLocation(
            $berlin,
            'heinrichplatz',
            'Heinrichplatz',
            'Startpunkt der Critical Mass Berlin in Kreuzberg.',
            52.4989,
            13.4178
        );
        $this->addReference(self::BERLIN_HEINRICHPLATZ_REFERENCE, $berlinHeinrichplatz);
        $manager->persist($berlinHeinrichplatz);

        $berlinBrandenburgerTor = $this->createLocation(
            $berlin,
            'brandenburger-tor',
            'Brandenburger Tor',
            'Bekanntes Wahrzeichen und beliebtes Ziel der Critical Mass.',
            52.5163,
            13.3777
        );
        $this->addReference(self::BERLIN_BRANDENBURGER_TOR_REFERENCE, $berlinBrandenburgerTor);
        $manager->persist($berlinBrandenburgerTor);

        $munichMarienplatz = $this->createLocation(
            $munich,
            'marienplatz',
            'Marienplatz',
            'Zentraler Treffpunkt im Herzen Münchens.',
            48.1371,
            11.5754
        );
        $this->addReference(self::MUNICH_MARIENPLATZ_REFERENCE, $munichMarienplatz);
        $manager->persist($munichMarienplatz);

        $kielAsmusBremer = $this->createLocation(
            $kiel,
            'asmus-bremer-platz',
            'Asmus-Bremer-Platz',
            'Startpunkt der Critical Mass Kiel in der Innenstadt.',
            54.3233,
            10.1359
        );
        $this->addReference(self::KIEL_ASMUS_BREMER_REFERENCE, $kielAsmusBremer);
        $manager->persist($kielAsmusBremer);

        $manager->flush();
    }

    private function createLocation(
        City $city,
        string $slug,
        string $title,
        string $description,
        float $latitude,
        float $longitude
    ): Location {
        return (new Location())
            ->setCity($city)
            ->setSlug($slug)
            ->setTitle($title)
            ->setDescription($description)
            ->setLatitude($latitude)
            ->setLongitude($longitude);
    }

    public function getDependencies(): array
    {
        return [
            CityFixtures::class,
        ];
    }
}
