<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Photo;
use App\Entity\Ride;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PhotoFixtures extends Fixture implements DependentFixtureInterface
{
    public const HAMBURG_PHOTO_1_REFERENCE = 'photo-hamburg-1';
    public const HAMBURG_PHOTO_2_REFERENCE = 'photo-hamburg-2';
    public const BERLIN_PHOTO_1_REFERENCE = 'photo-berlin-1';
    public const MUNICH_PHOTO_1_REFERENCE = 'photo-munich-1';

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

        $hamburgPhoto1 = $this->createPhoto(
            $hamburgRidePast,
            $regularUser,
            'hamburg_ride_001.jpg',
            53.5611,
            9.9895,
            'Start an der Moorweide',
            $hamburgRidePast->getDateTime()
        );
        $this->addReference(self::HAMBURG_PHOTO_1_REFERENCE, $hamburgPhoto1);
        $manager->persist($hamburgPhoto1);

        $hamburgPhoto2 = $this->createPhoto(
            $hamburgRidePast,
            $cyclistUser,
            'hamburg_ride_002.jpg',
            53.5520,
            9.9930,
            'Jungfernstieg',
            (clone $hamburgRidePast->getDateTime())->modify('+30 minutes')
        );
        $this->addReference(self::HAMBURG_PHOTO_2_REFERENCE, $hamburgPhoto2);
        $manager->persist($hamburgPhoto2);

        $berlinPhoto1 = $this->createPhoto(
            $berlinRidePast,
            $regularUser,
            'berlin_ride_001.jpg',
            52.4989,
            13.4178,
            'Heinrichplatz Kreuzberg',
            $berlinRidePast->getDateTime()
        );
        $this->addReference(self::BERLIN_PHOTO_1_REFERENCE, $berlinPhoto1);
        $manager->persist($berlinPhoto1);

        $munichPhoto1 = $this->createPhoto(
            $munichRidePast,
            $cyclistUser,
            'munich_ride_001.jpg',
            48.1371,
            11.5754,
            'Marienplatz',
            $munichRidePast->getDateTime()
        );
        $this->addReference(self::MUNICH_PHOTO_1_REFERENCE, $munichPhoto1);
        $manager->persist($munichPhoto1);

        $manager->flush();
    }

    private function createPhoto(
        Ride $ride,
        User $user,
        string $imageName,
        float $latitude,
        float $longitude,
        string $location,
        \DateTime $exifCreationDate
    ): Photo {
        return (new Photo())
            ->setRide($ride)
            ->setCity($ride->getCity())
            ->setUser($user)
            ->setImageName($imageName)
            ->setLatitude($latitude)
            ->setLongitude($longitude)
            ->setLocation($location)
            ->setExifCreationDate($exifCreationDate)
            ->setEnabled(true)
            ->setDeleted(false)
            ->setDescription('Test photo for ' . $ride->getTitle());
    }

    public function getDependencies(): array
    {
        return [
            RideFixtures::class,
            UserFixtures::class,
        ];
    }
}
