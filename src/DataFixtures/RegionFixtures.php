<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Region;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class RegionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $world = $this->createRegion($manager, 'World', 'world');

        $europe = $this->createRegion($manager, 'Europe', 'europe', $world);

        $germany = $this->createRegion($manager, 'Germany', 'germany', $europe);

        $schleswigHolstein = $this->createRegion($manager, 'Schleswig-Holstein', 'schleswig-holstein', $germany);
        $hamburg = $this->createRegion($manager, 'Hamburg', 'hamburg', $germany);
        $mecklenburgVorpommern = $this->createRegion($manager, 'Mecklenburg-Vorpommern', 'mecklenburg-vorpommern', $germany);
        $niedersachsen = $this->createRegion($manager, 'Niedersachsen', 'niedersachsen', $germany);
        $bremen = $this->createRegion($manager, 'Bremen', 'bremen', $germany);
        $brandenburg = $this->createRegion($manager, 'Brandenburg', 'brandenburg', $germany);
        $berlin = $this->createRegion($manager, 'Berlin', 'berlin', $germany);
        $nordrheinWestfalen = $this->createRegion($manager, 'Nordrhein-Westfalen', 'nordrhein-westfalen', $germany);
        $hessen = $this->createRegion($manager, 'Hessen', 'hessen', $germany);
        $sachsen = $this->createRegion($manager, 'Sachsen', 'sachsen', $germany);
        $sachsenAnhalt = $this->createRegion($manager, 'Sachsen-Anhalt', 'sachsen-anhalt', $germany);
        $thueringen = $this->createRegion($manager, 'Thüringen', 'thueringen', $germany);
        $bayern = $this->createRegion($manager, 'Bayern', 'bayern', $germany);
        $badenWuerttemberg = $this->createRegion($manager, 'Baden-Württemberg', 'baden-wuerttemberg', $germany);
        $saarland = $this->createRegion($manager, 'Saarland', 'saarland', $germany);
        $rheinlandPflanz = $this->createRegion($manager, 'Rheinland-Pfalz', 'rheinland-pfalz', $germany);

        $manager->flush();
    }

    protected function createRegion(ObjectManager $manager, string $name, string $slug, Region $parent = null): Region
    {
        $region = new Region();
        $region
            ->setName($name)
            ->setParent($parent)
            ->setSlug($slug);

        $manager->persist($region);

        $this->setReference(sprintf('region-%s', $slug), $region);

        return $region;
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}
