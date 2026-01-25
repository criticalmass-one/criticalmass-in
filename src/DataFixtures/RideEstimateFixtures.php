<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Ride;
use App\Entity\RideEstimate;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RideEstimateFixtures extends Fixture implements DependentFixtureInterface
{
    public const HAMBURG_ESTIMATE_1_REFERENCE = 'estimate-hamburg-1';
    public const HAMBURG_ESTIMATE_2_REFERENCE = 'estimate-hamburg-2';
    public const HAMBURG_ESTIMATE_3_REFERENCE = 'estimate-hamburg-3';
    public const BERLIN_ESTIMATE_1_REFERENCE = 'estimate-berlin-1';
    public const BERLIN_ESTIMATE_2_REFERENCE = 'estimate-berlin-2';
    public const MUNICH_ESTIMATE_1_REFERENCE = 'estimate-munich-1';

    public function load(ObjectManager $manager): void
    {
        /** @var User $adminUser */
        $adminUser = $this->getReference(UserFixtures::ADMIN_USER_REFERENCE, User::class);
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

        $hamburgEstimate1 = $this->createEstimate(
            $hamburgRidePast,
            $adminUser,
            240,
            15.5,
            2.5,
            'web'
        );
        $this->addReference(self::HAMBURG_ESTIMATE_1_REFERENCE, $hamburgEstimate1);
        $manager->persist($hamburgEstimate1);

        $hamburgEstimate2 = $this->createEstimate(
            $hamburgRidePast,
            $regularUser,
            260,
            16.0,
            2.75,
            'web'
        );
        $this->addReference(self::HAMBURG_ESTIMATE_2_REFERENCE, $hamburgEstimate2);
        $manager->persist($hamburgEstimate2);

        $hamburgEstimate3 = $this->createEstimate(
            $hamburgRidePast,
            $cyclistUser,
            250,
            15.2,
            2.5,
            'app'
        );
        $this->addReference(self::HAMBURG_ESTIMATE_3_REFERENCE, $hamburgEstimate3);
        $manager->persist($hamburgEstimate3);

        $berlinEstimate1 = $this->createEstimate(
            $berlinRidePast,
            $adminUser,
            480,
            18.0,
            3.0,
            'web'
        );
        $this->addReference(self::BERLIN_ESTIMATE_1_REFERENCE, $berlinEstimate1);
        $manager->persist($berlinEstimate1);

        $berlinEstimate2 = $this->createEstimate(
            $berlinRidePast,
            $regularUser,
            520,
            17.5,
            2.75,
            'web'
        );
        $this->addReference(self::BERLIN_ESTIMATE_2_REFERENCE, $berlinEstimate2);
        $manager->persist($berlinEstimate2);

        $munichEstimate1 = $this->createEstimate(
            $munichRidePast,
            $cyclistUser,
            310,
            12.0,
            2.0,
            'app'
        );
        $this->addReference(self::MUNICH_ESTIMATE_1_REFERENCE, $munichEstimate1);
        $manager->persist($munichEstimate1);

        $manager->flush();
    }

    private function createEstimate(
        Ride $ride,
        User $user,
        int $estimatedParticipants,
        float $estimatedDistance,
        float $estimatedDuration,
        string $source
    ): RideEstimate {
        return (new RideEstimate())
            ->setRide($ride)
            ->setUser($user)
            ->setDateTime($ride->getDateTime())
            ->setEstimatedParticipants($estimatedParticipants)
            ->setEstimatedDistance($estimatedDistance)
            ->setEstimatedDuration($estimatedDuration)
            ->setSource($source)
            ->setLatitude($ride->getLatitude())
            ->setLongitude($ride->getLongitude());
    }

    public function getDependencies(): array
    {
        return [
            RideFixtures::class,
            UserFixtures::class,
        ];
    }
}
