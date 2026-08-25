<?php declare(strict_types=1);

namespace Tests\Entity;

use App\Entity\Region;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Support\EntityIdHelper;

final class RegionTest extends TestCase
{
    private function world(): Region
    {
        $world = (new Region())->setName('World')->setSlug('world');
        EntityIdHelper::setId($world, 1);

        return $world;
    }

    #[Test]
    public function onlyRegionWithIdOneIsTheWorld(): void
    {
        $other = (new Region())->setName('Europe');
        EntityIdHelper::setId($other, 2);

        self::assertTrue($this->world()->isWorld());
        self::assertFalse($other->isWorld());
        self::assertFalse((new Region())->isWorld());
    }

    #[Test]
    public function levelIsTheDistanceToTheWorld(): void
    {
        $world = $this->world();
        $europe = (new Region())->setName('Europe')->setParent($world);
        $germany = (new Region())->setName('Germany')->setParent($europe);
        $hamburg = (new Region())->setName('Hamburg')->setParent($germany);

        self::assertTrue($world->isLevel(0));
        self::assertFalse($world->isLevel(1));

        self::assertTrue($europe->isLevel(1));
        self::assertFalse($europe->isLevel(0));
        self::assertFalse($europe->isLevel(2));

        self::assertTrue($germany->isLevel(2));
        self::assertTrue($hamburg->isLevel(3));
        self::assertFalse($hamburg->isLevel(2));
        self::assertFalse($hamburg->isLevel(4));
    }

    #[Test]
    public function detachedRegionIsNoLevelAtAll(): void
    {
        $orphan = (new Region())->setName('Orphan');
        EntityIdHelper::setId($orphan, 99);

        foreach ([0, 1, 2, 3] as $level) {
            self::assertFalse($orphan->isLevel($level), (string) $level);
        }
    }

    #[Test]
    public function stringRepresentationIsTheName(): void
    {
        self::assertSame('Europe', (string) (new Region())->setName('Europe'));
    }
}
