<?php declare(strict_types=1);

namespace Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserForumRankTest extends TestCase
{
    public function testFreshAccountsStartAtZero(): void
    {
        self::assertSame(0, (new User())->getForumPostCount());
        self::assertSame('Neuling', (new User())->getForumRank());
    }

    public function testRankGrowsWithTheCount(): void
    {
        $user = new User();

        $user->setForumPostCount(9);
        self::assertSame('Neuling', $user->getForumRank());

        $user->setForumPostCount(10);
        self::assertSame('Mitglied', $user->getForumRank());

        $user->setForumPostCount(100);
        self::assertSame('Stammgast', $user->getForumRank());

        $user->setForumPostCount(500);
        self::assertSame('Urgestein', $user->getForumRank());
    }

    public function testCounterCanBeRaisedAndLowered(): void
    {
        $user = new User();

        $user->incForumPostCount()->incForumPostCount();
        self::assertSame(2, $user->getForumPostCount());

        $user->decForumPostCount();
        self::assertSame(1, $user->getForumPostCount());
    }

    public function testCounterNeverGoesNegative(): void
    {
        $user = new User();

        // Alte Beitraege koennen vor der Zaehlung zurueckgezogen worden sein.
        $user->decForumPostCount()->decForumPostCount();

        self::assertSame(0, $user->getForumPostCount());
        self::assertSame(0, (new User())->setForumPostCount(-5)->getForumPostCount());
    }
}
