<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Photo;
use App\Entity\Ride;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class PhotoFixtures extends Fixture implements DependentFixtureInterface
{
    const PHOTOS_PER_RIDE = 50;
    const TEST_FILENAME = 'foo.jpeg';

    public function load(ObjectManager $manager): void
    {
        $rideIdentifierList = [
            'ride-hamburg-2011-06-24',
            'ride-hamburg-2022-09-01',
            'ride-berlin-2025-08-01',
            'ride-london-2019-04-01',
        ];

        foreach ($rideIdentifierList as $rideIdentifier) {
            /** @var Ride $ride */
            $ride = $this->getReference($rideIdentifier);

            for ($i = 1; $i <= self::PHOTOS_PER_RIDE; ++$i) {
                $photo = $this->createPhoto($ride, $i);

                $ride->addPhoto($photo);
                $manager->persist($photo);
            }
        }

        $manager->flush();
    }

    protected function createPhoto(Ride $ride, int $number): Photo
    {
        $dateIntervalSpec = sprintf('PT%dM', $number);
        $dateInterval = new \DateInterval($dateIntervalSpec);

        $dateTime = clone $ride->getDateTime();
        $dateTime->add($dateInterval);

        $latitude = $ride->getLatitude() + 0.001 * ($number - (self::PHOTOS_PER_RIDE / 2));
        $longitude = $ride->getLongitude() + 0.001 * ($number - (self::PHOTOS_PER_RIDE / 2));

        $photo = new Photo();

        $photo
            ->setRide($ride)
            ->setLatitude($latitude)
            ->setLongitude($longitude)
            ->setExifCreationDate($dateTime)
            ->setImageName(self::TEST_FILENAME)
            ->setEnabled(true);

        return $photo;
    }

    public function getDependencies(): array
    {
        return [
            RideFixtures::class,
        ];
    }
}
