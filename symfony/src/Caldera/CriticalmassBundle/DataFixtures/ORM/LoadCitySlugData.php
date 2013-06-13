<?php

namespace Caldera\CriticalmassBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Caldera\CriticalmassBundle\Entity\City;
use Caldera\CriticalmassBundle\Entity\CitySlug;

class LoadCitySlugData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$citySlug = new CitySlug();
		$citySlug->setCity($this->getReference("city-hamburg"));
		$citySlug->setSlug("hamburg");

		$manager->persist($citySlug);
		$manager->flush();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getOrder()
	{
		return 5;
	}
}
