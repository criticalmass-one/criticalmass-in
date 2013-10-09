<?php

namespace Caldera\CriticalmassCoreBundle\DataFixtures\ORM;

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
		$position->setLatitude(53.57805);
		$position->setLongitude(9.72877);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:00:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(53.57843);
		$position->setLongitude(9.72815);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:03:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(53.57859);
		$position->setLongitude(9.72804);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:06:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(53.57780);
		$position->setLongitude(9.72866);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:09:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(53.57734);
		$position->setLongitude(9.72421);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:12:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(53.57709);
		$position->setLongitude(9.72213);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:15:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(53.57570);
		$position->setLongitude(9.72157);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:18:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(53.57410);
		$position->setLongitude(9.72101);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:21:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(53.57447);
		$position->setLongitude(9.71772);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:24:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(53.57661);
		$position->setLongitude(9.71764);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:27:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(53.57988);
		$position->setLongitude(9.71751);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:30:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(53.58024);
		$position->setLongitude(9.72);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:33:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(53.58081);
		$position->setLongitude(9.72248);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:36:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(53.58244);
		$position->setLongitude(9.72559);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:39:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(53.58373);
		$position->setLongitude(9.7232);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:42:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(53.58481);
		$position->setLongitude(9.72308);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:45:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(53.58485);
		$position->setLongitude(9.72286);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:48:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(53.58375);
		$position->setLongitude(9.72334);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:51:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(53.58239);
		$position->setLongitude(9.71373);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:54:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(53.5814);
		$position->setLongitude(9.70358);
		$position->setCreationDateTime(new \DateTime("2013-05-31 19:57:00"));

		$manager->persist($position);
		$manager->flush();

		$position = new DefaultFixturePosition();
		$position->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$position->setUser($this->getReference("user-maltehuebner"));
		$position->setLatitude(53.58188);
		$position->setLongitude(9.70412);
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
