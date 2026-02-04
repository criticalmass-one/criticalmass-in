<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public const ADMIN_USER_REFERENCE = 'user-admin';
    public const REGULAR_USER_REFERENCE = 'user-regular';
    public const CYCLIST_USER_REFERENCE = 'user-cyclist';
    public const PHOTO_DOWNLOAD_USER_REFERENCE = 'user-photo-download';

    public function load(ObjectManager $manager): void
    {
        $adminUser = (new User())
            ->setUsername('admin')
            ->setEmail('admin@criticalmass.in')
            ->setRoles(['ROLE_ADMIN', 'ROLE_USER'])
            ->setEnabled(true)
            ->setColorRed(255)
            ->setColorGreen(0)
            ->setColorBlue(0);
        $manager->persist($adminUser);
        $this->addReference(self::ADMIN_USER_REFERENCE, $adminUser);

        $regularUser = (new User())
            ->setUsername('testuser')
            ->setEmail('testuser@criticalmass.in')
            ->setRoles(['ROLE_USER'])
            ->setEnabled(true)
            ->setColorRed(0)
            ->setColorGreen(255)
            ->setColorBlue(0);
        $manager->persist($regularUser);
        $this->addReference(self::REGULAR_USER_REFERENCE, $regularUser);

        $cyclistUser = (new User())
            ->setUsername('cyclist')
            ->setEmail('cyclist@criticalmass.in')
            ->setRoles(['ROLE_USER'])
            ->setEnabled(true)
            ->setColorRed(0)
            ->setColorGreen(0)
            ->setColorBlue(255);
        $manager->persist($cyclistUser);
        $this->addReference(self::CYCLIST_USER_REFERENCE, $cyclistUser);

        $photoDownloadUser = (new User())
            ->setUsername('photodownloader')
            ->setEmail('photodownloader@criticalmass.in')
            ->setRoles(['ROLE_PHOTO_DOWNLOAD'])
            ->setEnabled(true)
            ->setColorRed(128)
            ->setColorGreen(128)
            ->setColorBlue(0);
        $manager->persist($photoDownloadUser);
        $this->addReference(self::PHOTO_DOWNLOAD_USER_REFERENCE, $photoDownloadUser);

        $manager->flush();
    }
}
