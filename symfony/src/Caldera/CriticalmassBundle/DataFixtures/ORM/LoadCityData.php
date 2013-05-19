<?php

namespace Caldera\CriticalmassBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Caldera\CriticalmassBundle\Entity\City;

class LoadCityData implements FixtureInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$city = new City();
		$city->setCity("hamburg");
		$city->setTitle("Critical Mass Hamburg");
		$city->setUrl("http://www.criticalmass-hamburg.de/");
		$city->setFacebook("http://www.facebook.com/criticalmasshamburg");
		$city->setTwitter("http://www.twitter.com/cm_hh");

		$manager->persist($city);
		$manager->flush();
	}
}