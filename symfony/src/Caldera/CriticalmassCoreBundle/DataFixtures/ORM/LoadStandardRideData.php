<?php

namespace Caldera\CriticalmassCoreBundle\DataFixtures\ORM;

use Caldera\CriticalmassCoreBundle\Entity\StandardRide;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Caldera\CriticalmassCoreBundle\Entity\Ride;

class LoadStandardRideData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
        $standardRide = new StandardRide();
        $standardRide->setCity($this->getReference("city-wedel"));
        $standardRide->setTime(new \DateTime("19:00:00"));
        $standardRide->setLocation("Fachhochschule Wedel");
        $standardRide->setWeekday(5);
        $standardRide->setWeek(2);
        $standardRide->setLatitude(53.57803);
        $standardRide->setLongitude(9.7287);

        $manager->persist($standardRide);
        $manager->flush();


		// Beispiel-Touren der Critical Mass Hamburg
        $standardRide = new StandardRide();
        $standardRide->setCity($this->getReference("city-hamburg"));
        $standardRide->setTime(new \DateTime("19:00:00"));
        $standardRide->setLocation(null);
        $standardRide->setWeekday(5);
        $standardRide->setWeek(-1);

		$manager->persist($standardRide);
		$manager->flush();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getOrder()
	{
		return 2;
	}
}
