<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Rating;
use App\Entity\Ride;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RatingFixtures extends Fixture implements DependentFixtureInterface
{
    public const HAMBURG_RATING_1_REFERENCE = 'rating-hamburg-1';
    public const HAMBURG_RATING_2_REFERENCE = 'rating-hamburg-2';
    public const HAMBURG_RATING_3_REFERENCE = 'rating-hamburg-3';
    public const BERLIN_RATING_1_REFERENCE = 'rating-berlin-1';
    public const BERLIN_RATING_2_REFERENCE = 'rating-berlin-2';

    public function load(ObjectManager $manager): void
    {
        /** @var User $regularUser */
        $regularUser = $this->getReference(UserFixtures::REGULAR_USER_REFERENCE, User::class);
        /** @var User $cyclistUser */
        $cyclistUser = $this->getReference(UserFixtures::CYCLIST_USER_REFERENCE, User::class);
        /** @var User $adminUser */
        $adminUser = $this->getReference(UserFixtures::ADMIN_USER_REFERENCE, User::class);

        /** @var Ride $hamburgRidePast */
        $hamburgRidePast = $this->getReference(RideFixtures::HAMBURG_RIDE_PAST_REFERENCE, Ride::class);
        /** @var Ride $berlinRidePast */
        $berlinRidePast = $this->getReference(RideFixtures::BERLIN_RIDE_PAST_REFERENCE, Ride::class);

        // Hamburg ride ratings - average should be 4.0
        $hamburgRating1 = $this->createRating($hamburgRidePast, $regularUser, 5);
        $this->addReference(self::HAMBURG_RATING_1_REFERENCE, $hamburgRating1);
        $manager->persist($hamburgRating1);

        $hamburgRating2 = $this->createRating($hamburgRidePast, $cyclistUser, 4);
        $this->addReference(self::HAMBURG_RATING_2_REFERENCE, $hamburgRating2);
        $manager->persist($hamburgRating2);

        $hamburgRating3 = $this->createRating($hamburgRidePast, $adminUser, 3);
        $this->addReference(self::HAMBURG_RATING_3_REFERENCE, $hamburgRating3);
        $manager->persist($hamburgRating3);

        // Berlin ride ratings - average should be 4.5
        $berlinRating1 = $this->createRating($berlinRidePast, $regularUser, 5);
        $this->addReference(self::BERLIN_RATING_1_REFERENCE, $berlinRating1);
        $manager->persist($berlinRating1);

        $berlinRating2 = $this->createRating($berlinRidePast, $cyclistUser, 4);
        $this->addReference(self::BERLIN_RATING_2_REFERENCE, $berlinRating2);
        $manager->persist($berlinRating2);

        $manager->flush();
    }

    private function createRating(Ride $ride, User $user, int $stars): Rating
    {
        return (new Rating())
            ->setRide($ride)
            ->setUser($user)
            ->setRating($stars);
    }

    public function getDependencies(): array
    {
        return [
            RideFixtures::class,
            UserFixtures::class,
        ];
    }
}
