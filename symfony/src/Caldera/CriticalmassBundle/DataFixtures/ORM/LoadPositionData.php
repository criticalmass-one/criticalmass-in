<?php

namespace Caldera\CriticalmassBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Caldera\CriticalmassBundle\Entity\Position;

class LoadPositionData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$position = new Position();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setAccuracy(0.0);
		$position->setAltitude(0.0);
		$position->setAltitudeAccuracy(0.0);
		$position->setHeading(0.0);
		$position->setSpeed(0.0);
		$position->setTimestamp(0.0);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:00:00"));

		$manager->persist($position);
		$manager->flush();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getOrder()
	{
		return 4;
	}
}
