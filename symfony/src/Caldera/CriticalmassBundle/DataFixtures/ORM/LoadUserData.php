<?php

namespace Caldera\CriticalmassBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Caldera\CriticalmassBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$user = new User();

		$user->setUsername("maltehuebner");
		$user->setPassword("0580ba2f3f6dd5c563d85de947601e1b61add080");
		$user->setEmail("maltehuebner@gmx.org");

		$manager->persist($user);
		$manager->flush();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getOrder()
	{
		return 3;
	}
}
