<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user
            ->setUsername('maltehuebner')
            ->setEmail('maltehuebner@gmx.org')
            ->setPlainPassword('123456')
            ->setEnabled(true);

        $this->addReference('user-maltehuebner', $user);

        $manager->persist($user);
        $manager->flush();
    }
}
