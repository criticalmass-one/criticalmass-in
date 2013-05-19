<?php

namespace Caldera\CriticalmassBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Caldera\CriticalmassBundle\Entity\Ride;

class LoadRideData implements FixtureInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$ride = new Ride();
		$ride->setDate(new \DateTime("2013-05-31"));
		$ride->setTime(new \DateTime("19:00:00"));
		$ride->setLocation("tba");

		$manager->persist($ride);
		$manager->flush();
	}
}