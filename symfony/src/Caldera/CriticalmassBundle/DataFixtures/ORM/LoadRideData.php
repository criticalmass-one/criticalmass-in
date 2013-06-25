<?php

namespace Caldera\CriticalmassBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Caldera\CriticalmassBundle\Entity\Ride;

class LoadRideData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		// Beispiel-Touren der Critical Mass Hamburg
		$ride = new Ride();
		$ride->setCity($this->getReference("city-hamburg"));
		$ride->setDate(new \DateTime("2013-01-25"));
		$ride->setTime(new \DateTime("19:00:00"));
		$ride->setLocation("tba");
		$ride->setMap("");

		$manager->persist($ride);
		$manager->flush();

		$ride = new Ride();
		$ride->setCity($this->getReference("city-hamburg"));
		$ride->setDate(new \DateTime("2013-02-22"));
		$ride->setTime(new \DateTime("19:00:00"));
		$ride->setLocation("tba");
		$ride->setMap("");

		$manager->persist($ride);
		$manager->flush();

		$ride = new Ride();
		$ride->setCity($this->getReference("city-hamburg"));
		$ride->setDate(new \DateTime("2013-03-29"));
		$ride->setTime(new \DateTime("19:00:00"));
		$ride->setLocation("tba");
		$ride->setMap("");

		$manager->persist($ride);
		$manager->flush();

		$ride = new Ride();
		$ride->setCity($this->getReference("city-hamburg"));
		$ride->setDate(new \DateTime("2013-04-26"));
		$ride->setTime(new \DateTime("19:00:00"));
		$ride->setLocation("tba");
		$ride->setMap("");

		$manager->persist($ride);
		$manager->flush();

		$ride = new Ride();
		$ride->setCity($this->getReference("city-hamburg"));
		$ride->setDate(new \DateTime("2013-05-31"));
		$ride->setTime(new \DateTime("19:00:00"));
		$ride->setLocation("tba");
		$ride->setMap("");

		$manager->persist($ride);
		$manager->flush();

		$this->addReference("city-hamburg-ride-2013-05-31", $ride);

		$ride = new Ride();
		$ride->setCity($this->getReference("city-hamburg"));
		$ride->setDate(new \DateTime("2013-06-28"));
		$ride->setTime(new \DateTime("19:00:00"));
		$ride->setLocation("tba");
		$ride->setMap("");

		$manager->persist($ride);
		$manager->flush();

		$this->addReference("city-hamburg-ride-2013-06-28", $ride);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getOrder()
	{
		return 2;
	}
}
