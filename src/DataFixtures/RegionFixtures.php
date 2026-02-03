<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Region;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RegionFixtures extends Fixture
{
    public const WORLD_REFERENCE = 'region-world';
    public const EUROPE_REFERENCE = 'region-europe';
    public const GERMANY_REFERENCE = 'region-germany';
    public const BADEN_WUERTTEMBERG_REFERENCE = 'region-baden-wuerttemberg';
    public const BAYERN_REFERENCE = 'region-bayern';
    public const BERLIN_REFERENCE = 'region-berlin';
    public const BRANDENBURG_REFERENCE = 'region-brandenburg';
    public const BREMEN_REFERENCE = 'region-bremen';
    public const HAMBURG_REFERENCE = 'region-hamburg';
    public const HESSEN_REFERENCE = 'region-hessen';
    public const MECKLENBURG_VORPOMMERN_REFERENCE = 'region-mecklenburg-vorpommern';
    public const NIEDERSACHSEN_REFERENCE = 'region-niedersachsen';
    public const NORDRHEIN_WESTFALEN_REFERENCE = 'region-nordrhein-westfalen';
    public const RHEINLAND_PFALZ_REFERENCE = 'region-rheinland-pfalz';
    public const SAARLAND_REFERENCE = 'region-saarland';
    public const SACHSEN_REFERENCE = 'region-sachsen';
    public const SACHSEN_ANHALT_REFERENCE = 'region-sachsen-anhalt';
    public const SCHLESWIG_HOLSTEIN_REFERENCE = 'region-schleswig-holstein';
    public const THUERINGEN_REFERENCE = 'region-thueringen';

    public function load(ObjectManager $manager): void
    {
        $world = (new Region())
            ->setName('World')
            ->setSlug('world');
        $manager->persist($world);
        $this->addReference(self::WORLD_REFERENCE, $world);

        $europe = (new Region())
            ->setName('Europe')
            ->setSlug('europe')
            ->setParent($world);
        $manager->persist($europe);
        $this->addReference(self::EUROPE_REFERENCE, $europe);

        $germany = (new Region())
            ->setName('Germany')
            ->setSlug('germany')
            ->setParent($europe);
        $manager->persist($germany);
        $this->addReference(self::GERMANY_REFERENCE, $germany);

        $badenWuerttemberg = (new Region())
            ->setName('Baden-Württemberg')
            ->setSlug('baden-wuerttemberg')
            ->setParent($germany);
        $manager->persist($badenWuerttemberg);
        $this->addReference(self::BADEN_WUERTTEMBERG_REFERENCE, $badenWuerttemberg);

        $bayern = (new Region())
            ->setName('Bayern')
            ->setSlug('bayern')
            ->setParent($germany);
        $manager->persist($bayern);
        $this->addReference(self::BAYERN_REFERENCE, $bayern);

        $berlin = (new Region())
            ->setName('Berlin')
            ->setSlug('berlin')
            ->setParent($germany);
        $manager->persist($berlin);
        $this->addReference(self::BERLIN_REFERENCE, $berlin);

        $brandenburg = (new Region())
            ->setName('Brandenburg')
            ->setSlug('brandenburg')
            ->setParent($germany);
        $manager->persist($brandenburg);
        $this->addReference(self::BRANDENBURG_REFERENCE, $brandenburg);

        $bremen = (new Region())
            ->setName('Bremen')
            ->setSlug('bremen')
            ->setParent($germany);
        $manager->persist($bremen);
        $this->addReference(self::BREMEN_REFERENCE, $bremen);

        $hamburg = (new Region())
            ->setName('Hamburg')
            ->setSlug('hamburg')
            ->setParent($germany);
        $manager->persist($hamburg);
        $this->addReference(self::HAMBURG_REFERENCE, $hamburg);

        $hessen = (new Region())
            ->setName('Hessen')
            ->setSlug('hessen')
            ->setParent($germany);
        $manager->persist($hessen);
        $this->addReference(self::HESSEN_REFERENCE, $hessen);

        $mecklenburgVorpommern = (new Region())
            ->setName('Mecklenburg-Vorpommern')
            ->setSlug('mecklenburg-vorpommern')
            ->setParent($germany);
        $manager->persist($mecklenburgVorpommern);
        $this->addReference(self::MECKLENBURG_VORPOMMERN_REFERENCE, $mecklenburgVorpommern);

        $niedersachsen = (new Region())
            ->setName('Niedersachsen')
            ->setSlug('niedersachsen')
            ->setParent($germany);
        $manager->persist($niedersachsen);
        $this->addReference(self::NIEDERSACHSEN_REFERENCE, $niedersachsen);

        $nordrheinWestfalen = (new Region())
            ->setName('Nordrhein-Westfalen')
            ->setSlug('nordrhein-westfalen')
            ->setParent($germany);
        $manager->persist($nordrheinWestfalen);
        $this->addReference(self::NORDRHEIN_WESTFALEN_REFERENCE, $nordrheinWestfalen);

        $rheinlandPfalz = (new Region())
            ->setName('Rheinland-Pfalz')
            ->setSlug('rheinland-pfalz')
            ->setParent($germany);
        $manager->persist($rheinlandPfalz);
        $this->addReference(self::RHEINLAND_PFALZ_REFERENCE, $rheinlandPfalz);

        $saarland = (new Region())
            ->setName('Saarland')
            ->setSlug('saarland')
            ->setParent($germany);
        $manager->persist($saarland);
        $this->addReference(self::SAARLAND_REFERENCE, $saarland);

        $sachsen = (new Region())
            ->setName('Sachsen')
            ->setSlug('sachsen')
            ->setParent($germany);
        $manager->persist($sachsen);
        $this->addReference(self::SACHSEN_REFERENCE, $sachsen);

        $sachsenAnhalt = (new Region())
            ->setName('Sachsen-Anhalt')
            ->setSlug('sachsen-anhalt')
            ->setParent($germany);
        $manager->persist($sachsenAnhalt);
        $this->addReference(self::SACHSEN_ANHALT_REFERENCE, $sachsenAnhalt);

        $schleswigHolstein = (new Region())
            ->setName('Schleswig-Holstein')
            ->setSlug('schleswig-holstein')
            ->setParent($germany);
        $manager->persist($schleswigHolstein);
        $this->addReference(self::SCHLESWIG_HOLSTEIN_REFERENCE, $schleswigHolstein);

        $thueringen = (new Region())
            ->setName('Thüringen')
            ->setSlug('thueringen')
            ->setParent($germany);
        $manager->persist($thueringen);
        $this->addReference(self::THUERINGEN_REFERENCE, $thueringen);

        $manager->flush();
    }
}
