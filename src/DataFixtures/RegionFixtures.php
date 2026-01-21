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
    public const SCHLESWIG_HOLSTEIN_REFERENCE = 'region-schleswig-holstein';
    public const HAMBURG_REFERENCE = 'region-hamburg';
    public const BERLIN_REFERENCE = 'region-berlin';
    public const BAYERN_REFERENCE = 'region-bayern';

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

        $schleswigHolstein = (new Region())
            ->setName('Schleswig-Holstein')
            ->setSlug('schleswig-holstein')
            ->setParent($germany);
        $manager->persist($schleswigHolstein);
        $this->addReference(self::SCHLESWIG_HOLSTEIN_REFERENCE, $schleswigHolstein);

        $hamburg = (new Region())
            ->setName('Hamburg')
            ->setSlug('hamburg')
            ->setParent($germany);
        $manager->persist($hamburg);
        $this->addReference(self::HAMBURG_REFERENCE, $hamburg);

        $berlin = (new Region())
            ->setName('Berlin')
            ->setSlug('berlin')
            ->setParent($germany);
        $manager->persist($berlin);
        $this->addReference(self::BERLIN_REFERENCE, $berlin);

        $bayern = (new Region())
            ->setName('Bayern')
            ->setSlug('bayern')
            ->setParent($germany);
        $manager->persist($bayern);
        $this->addReference(self::BAYERN_REFERENCE, $bayern);

        $manager->flush();
    }
}
