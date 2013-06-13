<?php

namespace Caldera\CriticalmassBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Caldera\CriticalmassBundle\Entity\DefaultFixturePosition;

class LoadPositionData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:00:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:03:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:06:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:09:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:12:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:15:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:18:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:21:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:24:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:27:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:30:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:33:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:36:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:39:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:42:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:45:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:48:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:51:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:54:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:57:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72877);
		$position->setLongitude(53.57805);
		$position->setCreationDateTime(new \DateTime("2013-05-31 20:00:00"));

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
