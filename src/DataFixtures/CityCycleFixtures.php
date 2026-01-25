<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CityCycleFixtures extends Fixture implements DependentFixtureInterface
{
    public const HAMBURG_CYCLE_REFERENCE = 'cycle-hamburg';
    public const BERLIN_CYCLE_REFERENCE = 'cycle-berlin';
    public const MUNICH_CYCLE_REFERENCE = 'cycle-munich';
    public const KIEL_CYCLE_REFERENCE = 'cycle-kiel';

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
        /** @var User $adminUser */
        $adminUser = $this->getReference(UserFixtures::ADMIN_USER_REFERENCE, User::class);

        $hamburgCycle = $this->createCycle(
            $hamburg,
            $adminUser,
            CityCycle::DAY_FRIDAY,
            CityCycle::WEEK_LAST,
            new \DateTime('19:00:00'),
            'Moorweide',
            53.5611,
            9.9895
        );
        $this->addReference(self::HAMBURG_CYCLE_REFERENCE, $hamburgCycle);
        $manager->persist($hamburgCycle);

        $berlinCycle = $this->createCycle(
            $berlin,
            $adminUser,
            CityCycle::DAY_FRIDAY,
            CityCycle::WEEK_LAST,
            new \DateTime('19:00:00'),
            'Heinrichplatz',
            52.4989,
            13.4178
        );
        $this->addReference(self::BERLIN_CYCLE_REFERENCE, $berlinCycle);
        $manager->persist($berlinCycle);

        $munichCycle = $this->createCycle(
            $munich,
            $adminUser,
            CityCycle::DAY_FRIDAY,
            CityCycle::WEEK_LAST,
            new \DateTime('19:00:00'),
            'Marienplatz',
            48.1371,
            11.5754
        );
        $this->addReference(self::MUNICH_CYCLE_REFERENCE, $munichCycle);
        $manager->persist($munichCycle);

        $kielCycle = $this->createCycle(
            $kiel,
            $adminUser,
            CityCycle::DAY_FRIDAY,
            CityCycle::WEEK_LAST,
            new \DateTime('18:00:00'),
            'Asmus-Bremer-Platz',
            54.3233,
            10.1359
        );
        $this->addReference(self::KIEL_CYCLE_REFERENCE, $kielCycle);
        $manager->persist($kielCycle);

        $manager->flush();
    }

    private function createCycle(
        City $city,
        User $user,
        int $dayOfWeek,
        int $weekOfMonth,
        \DateTime $time,
        string $location,
        float $latitude,
        float $longitude
    ): CityCycle {
        return (new CityCycle())
            ->setCity($city)
            ->setUser($user)
            ->setDayOfWeek($dayOfWeek)
            ->setWeekOfMonth($weekOfMonth)
            ->setTime($time)
            ->setLocation($location)
            ->setLatitude($latitude)
            ->setLongitude($longitude)
            ->setValidFrom(new \DateTime('2020-01-01'));
    }

    public function getDependencies(): array
    {
        return [
            CityFixtures::class,
            UserFixtures::class,
        ];
    }
}
