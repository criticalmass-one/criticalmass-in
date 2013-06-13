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

		$user = new User();

		$user->setUsername("TestUser1");
		$user->setPassword("ffdddfbcb81c43ca2df23037b87553249da33549");
		$user->setEmail("testuser1@criticalmass.in");

		$manager->persist($user);
		$manager->flush();

		$user = new User();

		$user->setUsername("TestUser2");
		$user->setPassword("017e8b6513f6dbafe3377cd7c4f271222ea89dcc");
		$user->setEmail("testuser2@criticalmass.in");

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
