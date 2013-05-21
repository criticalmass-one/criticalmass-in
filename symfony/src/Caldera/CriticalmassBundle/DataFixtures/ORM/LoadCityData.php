<?php

namespace Caldera\CriticalmassBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Caldera\CriticalmassBundle\Entity\City;

class LoadCityData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		// Critical Mass Hamburg
		$city = new City();
		$city->setCity("hamburg");
		$city->setTitle("Critical Mass Hamburg");
		$city->setUrl("http://www.criticalmass-hamburg.de/");
		$city->setFacebook("http://www.facebook.com/criticalmasshamburg");
		$city->setTwitter("http://www.twitter.com/cm_hh");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-hamburg", $city);

		// Critical Mass Bremen
		$city = new City();
		$city->setCity("bremen");
		$city->setTitle("Critical Mass Bremen");
		$city->setUrl("http://www.criticalmass-bremen.de/");
		$city->setFacebook("https://www.facebook.com/CriticalMassBremen");
		$city->setTwitter("");

		$manager->persist($city);
		$manager->flush();

		$this->addReference("city-bremen", $city);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getOrder()
	{
		return 1;
	}
}