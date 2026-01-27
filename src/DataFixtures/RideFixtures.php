<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\Ride;
use App\Entity\User;
use Carbon\Carbon;
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

        // Create rides for the last 3 months and next 3 months for Hamburg
        $hamburgParticipants = [280, 250, 220, null, null, null];
        for ($i = -3; $i <= 3; $i++) {
            if ($i === 0) {
                continue;
            }

            $monthOffset = $i > 0 ? "+{$i}" : (string) $i;
            $date = Carbon::parse("{$monthOffset} months last friday 19:00");
            $participantIndex = $i + 3;
            if ($i > 0) {
                $participantIndex--;
            }

            $ride = $this->createRide(
                $hamburg,
                $hamburgCycle,
                $adminUser,
                $date,
                'Critical Mass Hamburg ' . $date->format('F Y'),
                'Moorweide',
                53.5611,
                9.9895,
                $hamburgParticipants[$participantIndex] ?? null
            );

            if ($i === -1) {
                $this->addReference(self::HAMBURG_RIDE_PAST_REFERENCE, $ride);
            } elseif ($i === 1) {
                $this->addReference(self::HAMBURG_RIDE_FUTURE_REFERENCE, $ride);
            }

            $manager->persist($ride);
        }

        // Create rides for the last 3 months and next 3 months for Berlin
        $berlinParticipants = [600, 500, 450, null, null, null];
        for ($i = -3; $i <= 3; $i++) {
            if ($i === 0) {
                continue;
            }

            $monthOffset = $i > 0 ? "+{$i}" : (string) $i;
            $date = Carbon::parse("{$monthOffset} months last friday 19:00");
            $participantIndex = $i + 3;
            if ($i > 0) {
                $participantIndex--;
            }

            $ride = $this->createRide(
                $berlin,
                $berlinCycle,
                $adminUser,
                $date,
                'Critical Mass Berlin ' . $date->format('F Y'),
                'Heinrichplatz',
                52.4989,
                13.4178,
                $berlinParticipants[$participantIndex] ?? null
            );

            if ($i === -1) {
                $this->addReference(self::BERLIN_RIDE_PAST_REFERENCE, $ride);
            } elseif ($i === 1) {
                $this->addReference(self::BERLIN_RIDE_FUTURE_REFERENCE, $ride);
            }

            $manager->persist($ride);
        }

        // Create rides for the last 3 months and next 3 months for Munich
        $munichParticipants = [350, 300, 280, null, null, null];
        for ($i = -3; $i <= 3; $i++) {
            if ($i === 0) {
                continue;
            }

            $monthOffset = $i > 0 ? "+{$i}" : (string) $i;
            $date = Carbon::parse("{$monthOffset} months last friday 19:00");
            $participantIndex = $i + 3;
            if ($i > 0) {
                $participantIndex--;
            }

            $ride = $this->createRide(
                $munich,
                $munichCycle,
                $adminUser,
                $date,
                'Critical Mass Munich ' . $date->format('F Y'),
                'Marienplatz',
                48.1371,
                11.5754,
                $munichParticipants[$participantIndex] ?? null
            );

            if ($i === -2) {
                $this->addReference(self::MUNICH_RIDE_PAST_REFERENCE, $ride);
            }

            $manager->persist($ride);
        }

        // Create rides for the last 3 months and next 3 months for Kiel
        $kielParticipants = [120, 100, 80, null, null, null];
        for ($i = -3; $i <= 3; $i++) {
            if ($i === 0) {
                continue;
            }

            $monthOffset = $i > 0 ? "+{$i}" : (string) $i;
            $date = Carbon::parse("{$monthOffset} months last friday 18:00");
            $participantIndex = $i + 3;
            if ($i > 0) {
                $participantIndex--;
            }

            $ride = $this->createRide(
                $kiel,
                $kielCycle,
                $adminUser,
                $date,
                'Critical Mass Kiel ' . $date->format('F Y'),
                'Asmus-Bremer-Platz',
                54.3233,
                10.1359,
                $kielParticipants[$participantIndex] ?? null
            );

            if ($i === 2) {
                $this->addReference(self::KIEL_RIDE_FUTURE_REFERENCE, $ride);
            }

            $manager->persist($ride);
        }

        $manager->flush();
    }

    private function createRide(
        City $city,
        CityCycle $cycle,
        User $user,
        Carbon $dateTime,
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
