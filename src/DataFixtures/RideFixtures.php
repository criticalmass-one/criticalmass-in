<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Ride;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class RideFixtures extends Fixture implements DependentFixtureInterface
{
    public const HAMBURG_RIDE_PAST_REFERENCE = 'ride-hamburg-past';
    public const HAMBURG_RIDE_FUTURE_REFERENCE = 'ride-hamburg-future';
    public const BERLIN_RIDE_PAST_REFERENCE = 'ride-berlin-past';
    public const BERLIN_RIDE_FUTURE_REFERENCE = 'ride-berlin-future';
    public const MUNICH_RIDE_PAST_REFERENCE = 'ride-munich-past';
    public const KIEL_RIDE_FUTURE_REFERENCE = 'ride-kiel-future';

    public function load(ObjectManager $manager): void
    {
        /** @var City $hamburg */
        $hamburg = $this->getReference(CityFixtures::HAMBURG_REFERENCE, City::class);
        /** @var City $berlin */
        $berlin = $this->getReference(CityFixtures::BERLIN_REFERENCE, City::class);
        /** @var City $munich */
        $munich = $this->getReference(CityFixtures::MUNICH_REFERENCE, City::class);
        /** @var City $kiel */
        $kiel = $this->getReference(CityFixtures::KIEL_REFERENCE, City::class);

        /** @var CityCycle $hamburgCycle */
        $hamburgCycle = $this->getReference(CityCycleFixtures::HAMBURG_CYCLE_REFERENCE, CityCycle::class);
        /** @var CityCycle $berlinCycle */
        $berlinCycle = $this->getReference(CityCycleFixtures::BERLIN_CYCLE_REFERENCE, CityCycle::class);
        /** @var CityCycle $munichCycle */
        $munichCycle = $this->getReference(CityCycleFixtures::MUNICH_CYCLE_REFERENCE, CityCycle::class);
        /** @var CityCycle $kielCycle */
        $kielCycle = $this->getReference(CityCycleFixtures::KIEL_CYCLE_REFERENCE, CityCycle::class);

        /** @var User $adminUser */
        $adminUser = $this->getReference(UserFixtures::ADMIN_USER_REFERENCE, User::class);

        $pastDate = new \DateTime('-1 month last friday 19:00');
        $futureDate = new \DateTime('+1 month last friday 19:00');

        $hamburgRidePast = $this->createRide(
            $hamburg,
            $hamburgCycle,
            $adminUser,
            $pastDate,
            'Critical Mass Hamburg ' . $pastDate->format('F Y'),
            'Moorweide',
            53.5611,
            9.9895,
            250
        );
        $this->addReference(self::HAMBURG_RIDE_PAST_REFERENCE, $hamburgRidePast);
        $manager->persist($hamburgRidePast);

        $hamburgRideFuture = $this->createRide(
            $hamburg,
            $hamburgCycle,
            $adminUser,
            $futureDate,
            'Critical Mass Hamburg ' . $futureDate->format('F Y'),
            'Moorweide',
            53.5611,
            9.9895
        );
        $this->addReference(self::HAMBURG_RIDE_FUTURE_REFERENCE, $hamburgRideFuture);
        $manager->persist($hamburgRideFuture);

        $berlinPastDate = new \DateTime('-1 month last friday 19:00');
        $berlinFutureDate = new \DateTime('+1 month last friday 19:00');

        $berlinRidePast = $this->createRide(
            $berlin,
            $berlinCycle,
            $adminUser,
            $berlinPastDate,
            'Critical Mass Berlin ' . $berlinPastDate->format('F Y'),
            'Heinrichplatz',
            52.4989,
            13.4178,
            500
        );
        $this->addReference(self::BERLIN_RIDE_PAST_REFERENCE, $berlinRidePast);
        $manager->persist($berlinRidePast);

        $berlinRideFuture = $this->createRide(
            $berlin,
            $berlinCycle,
            $adminUser,
            $berlinFutureDate,
            'Critical Mass Berlin ' . $berlinFutureDate->format('F Y'),
            'Heinrichplatz',
            52.4989,
            13.4178
        );
        $this->addReference(self::BERLIN_RIDE_FUTURE_REFERENCE, $berlinRideFuture);
        $manager->persist($berlinRideFuture);

        $munichPastDate = new \DateTime('-2 months last friday 19:00');

        $munichRidePast = $this->createRide(
            $munich,
            $munichCycle,
            $adminUser,
            $munichPastDate,
            'Critical Mass Munich ' . $munichPastDate->format('F Y'),
            'Marienplatz',
            48.1371,
            11.5754,
            300
        );
        $this->addReference(self::MUNICH_RIDE_PAST_REFERENCE, $munichRidePast);
        $manager->persist($munichRidePast);

        $kielFutureDate = new \DateTime('+2 months last friday 18:00');

        $kielRideFuture = $this->createRide(
            $kiel,
            $kielCycle,
            $adminUser,
            $kielFutureDate,
            'Critical Mass Kiel ' . $kielFutureDate->format('F Y'),
            'Asmus-Bremer-Platz',
            54.3233,
            10.1359
        );
        $this->addReference(self::KIEL_RIDE_FUTURE_REFERENCE, $kielRideFuture);
        $manager->persist($kielRideFuture);

        $manager->flush();
    }

    private function createRide(
        City $city,
        CityCycle $cycle,
        User $user,
        \DateTime $dateTime,
        string $title,
        string $location,
        float $latitude,
        float $longitude,
        ?int $estimatedParticipants = null
    ): Ride {
        $ride = (new Ride())
            ->setCity($city)
            ->setCycle($cycle)
            ->setUser($user)
            ->setDateTime($dateTime)
            ->setTitle($title)
            ->setLocation($location)
            ->setLatitude($latitude)
            ->setLongitude($longitude)
            ->setEnabled(true);

        if ($estimatedParticipants !== null) {
            $ride->setEstimatedParticipants($estimatedParticipants);
        }

        return $ride;
    }

    public function getDependencies(): array
    {
        return [
            CityFixtures::class,
            CityCycleFixtures::class,
            UserFixtures::class,
        ];
    }
}
