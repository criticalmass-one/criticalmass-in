<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Board;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BoardFixtures extends Fixture
{
    public const GENERAL_BOARD_REFERENCE = 'board-general';

    public function load(ObjectManager $manager): void
    {
        $board = new Board();
        $board->setSlug('general');
        $board->setEnabled(true);
        $board->setPosition(1);
        $board->setTitle('Allgemeine Diskussion');

        $manager->persist($board);
        $manager->flush();

        $this->addReference(self::GENERAL_BOARD_REFERENCE, $board);
    }
}
