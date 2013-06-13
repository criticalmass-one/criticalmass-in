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
		$position->setLatitude(9.72815);
		$position->setLongitude(53.57843);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:03:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72804);
		$position->setLongitude(53.57859);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:06:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72866);
		$position->setLongitude(53.57780);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:09:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72421);
		$position->setLongitude(53.57734);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:12:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72213);
		$position->setLongitude(53.57709);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:15:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72157);
		$position->setLongitude(53.57570);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:18:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72101);
		$position->setLongitude(53.57410);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:21:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.71772);
		$position->setLongitude(53.57447);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:24:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.71764);
		$position->setLongitude(53.57661);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:27:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.71751);
		$position->setLongitude(53.57988);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:30:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72);
		$position->setLongitude(53.58024);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:33:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72248);
		$position->setLongitude(53.58081);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:36:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72559);
		$position->setLongitude(53.58244);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:39:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.7232);
		$position->setLongitude(53.58373);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:42:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72308);
		$position->setLongitude(53.58481);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:45:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72286);
		$position->setLongitude(53.58485);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:48:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.72334);
		$position->setLongitude(53.58375);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:51:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.71373);
		$position->setLongitude(53.58239);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:54:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.70358);
		$position->setLongitude(53.5814);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:57:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(9.70412);
		$position->setLongitude(53.58188);
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
