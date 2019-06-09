<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\CityCycle;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CityCycleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $hamburg = new CityCycle();
        $hamburg
            ->setCity($this->getReference('city-hamburg'))
            ->setDayOfWeek(CityCycle::DAY_FRIDAY)
            ->setWeekOfMonth(CityCycle::WEEK_LAST)
            ->setTime(new \DateTime('18:00'));

        $manager->persist($hamburg);

        $berlin1 = new CityCycle();
        $berlin1
            ->setCity($this->getReference('city-berlin'))
            ->setDayOfWeek(CityCycle::DAY_FRIDAY)
            ->setWeekOfMonth(CityCycle::WEEK_LAST)
            ->setTime(new \DateTime('20:00'));

        $manager->persist($berlin1);

        $berlin2 = new CityCycle();
        $berlin2
            ->setCity($this->getReference('city-berlin'))
            ->setDayOfWeek(CityCycle::DAY_SUNDAY)
            ->setWeekOfMonth(CityCycle::WEEK_FIRST)
            ->setTime(new \DateTime('14:00'));

        $manager->persist($berlin2);

        $halle = new CityCycle();
        $halle
            ->setCity($this->getReference('city-halle'))
            ->setDayOfWeek(CityCycle::DAY_FRIDAY)
            ->setWeekOfMonth(CityCycle::WEEK_LAST)
            ->setTime(new \DateTime('18:00'))
            ->setValidFrom(new \DateTime('2018-01-01'));

        $manager->persist($halle);

        $mainz1 = new CityCycle();
        $mainz1
            ->setCity($this->getReference('city-mainz'))
            ->setDayOfWeek(CityCycle::DAY_FRIDAY)
            ->setWeekOfMonth(CityCycle::WEEK_LAST)
            ->setTime(new \DateTime('18:00'))
            ->setValidUntil(new \DateTime('2018-09-30'));

        $manager->persist($mainz1);

        $mainz2 = new CityCycle();
        $mainz2
            ->setCity($this->getReference('city-mainz'))
            ->setDayOfWeek(CityCycle::DAY_FRIDAY)
            ->setWeekOfMonth(CityCycle::WEEK_LAST)
            ->setTime(new \DateTime('18:00'))
            ->setValidFrom(new \DateTime('2018-10-01'))
            ->setValidUntil(new \DateTime('2019-03-31'));

        $manager->persist($mainz2);

        $london = new CityCycle();
        $london
            ->setCity($this->getReference('city-london'))
            ->setDayOfWeek(CityCycle::DAY_FRIDAY)
            ->setWeekOfMonth(CityCycle::WEEK_LAST)
            ->setTime(new \DateTime('19:00'));

        $manager->persist($london);

        $esslingen = new CityCycle();
        $esslingen
            ->setCity($this->getReference('city-esslingen'))
            ->setDayOfWeek(CityCycle::DAY_FRIDAY)
            ->setWeekOfMonth(CityCycle::WEEK_SECOND)
            ->setTime(new \DateTime('18:00'))
            ->setDisabledAt(new \DateTime('2017-12-31'));

        $manager->persist($london);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CityFixtures::class,
            UserFixtures::class,
        ];
    }
}
