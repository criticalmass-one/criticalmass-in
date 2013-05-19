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
		$ride = new Ride();
		$ride->setCityId($this->getReference("city-hamburg"));
		$ride->setDate(new \DateTime("2013-05-31"));
		$ride->setTime(new \DateTime("19:00:00"));
		$ride->setLocation("tba");

		$manager->persist($ride);
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
