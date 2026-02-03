<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Post;
use App\Entity\Ride;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public const HAMBURG_POST_1_REFERENCE = 'post-hamburg-1';
    public const HAMBURG_POST_2_REFERENCE = 'post-hamburg-2';
    public const BERLIN_POST_1_REFERENCE = 'post-berlin-1';
    public const MUNICH_POST_1_REFERENCE = 'post-munich-1';
    public const DISABLED_POST_REFERENCE = 'post-disabled';
    public const PHOTO_POST_REFERENCE = 'post-photo';

    public function load(ObjectManager $manager): void
    {
        /** @var User $regularUser */
        $regularUser = $this->getReference(UserFixtures::REGULAR_USER_REFERENCE, User::class);
        /** @var User $cyclistUser */
        $cyclistUser = $this->getReference(UserFixtures::CYCLIST_USER_REFERENCE, User::class);

        /** @var Ride $hamburgRidePast */
        $hamburgRidePast = $this->getReference(RideFixtures::HAMBURG_RIDE_PAST_REFERENCE, Ride::class);
        /** @var Ride $berlinRidePast */
        $berlinRidePast = $this->getReference(RideFixtures::BERLIN_RIDE_PAST_REFERENCE, Ride::class);
        /** @var Ride $munichRidePast */
        $munichRidePast = $this->getReference(RideFixtures::MUNICH_RIDE_PAST_REFERENCE, Ride::class);

        /** @var Photo $hamburgPhoto1 */
        $hamburgPhoto1 = $this->getReference(PhotoFixtures::HAMBURG_PHOTO_1_REFERENCE, Photo::class);

        $hamburgDateTime = $hamburgRidePast->getDateTime() ?? new \DateTime();
        $berlinDateTime = $berlinRidePast->getDateTime() ?? new \DateTime();
        $munichDateTime = $munichRidePast->getDateTime() ?? new \DateTime();

        $hamburgPost1 = $this->createRidePost(
            $hamburgRidePast,
            $regularUser,
            'Tolle Tour heute! Das Wetter war perfekt.',
            53.5611,
            9.9895,
            (clone $hamburgDateTime)->modify('+1 hour')
        );
        $this->addReference(self::HAMBURG_POST_1_REFERENCE, $hamburgPost1);
        $manager->persist($hamburgPost1);

        $hamburgPost2 = $this->createRidePost(
            $hamburgRidePast,
            $cyclistUser,
            'War meine erste Critical Mass - super Erlebnis!',
            53.5520,
            9.9930,
            (clone $hamburgDateTime)->modify('+90 minutes')
        );
        $this->addReference(self::HAMBURG_POST_2_REFERENCE, $hamburgPost2);
        $manager->persist($hamburgPost2);

        $berlinPost1 = $this->createRidePost(
            $berlinRidePast,
            $regularUser,
            'Berlin rockt! Mehr als 500 Radler heute.',
            52.4989,
            13.4178,
            (clone $berlinDateTime)->modify('+45 minutes')
        );
        $this->addReference(self::BERLIN_POST_1_REFERENCE, $berlinPost1);
        $manager->persist($berlinPost1);

        $munichPost1 = $this->createRidePost(
            $munichRidePast,
            $cyclistUser,
            'Erste CM in Muenchen dieses Jahr!',
            48.1371,
            11.5754,
            (clone $munichDateTime)->modify('+30 minutes')
        );
        $this->addReference(self::MUNICH_POST_1_REFERENCE, $munichPost1);
        $manager->persist($munichPost1);

        $disabledPost = $this->createRidePost(
            $hamburgRidePast,
            $regularUser,
            'Dieser Post ist deaktiviert.',
            53.5600,
            9.9800,
            (clone $hamburgDateTime)->modify('+2 hours')
        );
        $disabledPost->setEnabled(false);
        $this->addReference(self::DISABLED_POST_REFERENCE, $disabledPost);
        $manager->persist($disabledPost);

        $photoPost = $this->createPhotoPost(
            $hamburgPhoto1,
            $cyclistUser,
            'Super Foto! War ein tolles Event.',
            (clone $hamburgDateTime)->modify('+3 hours')
        );
        $this->addReference(self::PHOTO_POST_REFERENCE, $photoPost);
        $manager->persist($photoPost);

        $manager->flush();
    }

    private function createRidePost(
        Ride $ride,
        User $user,
        string $message,
        float $latitude,
        float $longitude,
        \DateTime $dateTime
    ): Post {
        return (new Post())
            ->setRide($ride)
            ->setCity($ride->getCity())
            ->setUser($user)
            ->setMessage($message)
            ->setLatitude($latitude)
            ->setLongitude($longitude)
            ->setDateTime($dateTime)
            ->setEnabled(true);
    }

    private function createPhotoPost(
        Photo $photo,
        User $user,
        string $message,
        \DateTime $dateTime
    ): Post {
        return (new Post())
            ->setPhoto($photo)
            ->setUser($user)
            ->setMessage($message)
            ->setDateTime($dateTime)
            ->setEnabled(true);
    }

    public function getDependencies(): array
    {
        return [
            RideFixtures::class,
            UserFixtures::class,
            PhotoFixtures::class,
        ];
    }
}
