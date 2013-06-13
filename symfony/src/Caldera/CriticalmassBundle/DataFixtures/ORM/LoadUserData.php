<?php

namespace Caldera\CriticalmassBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Caldera\CriticalmassBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * {@inheritDoc}
	 */
	public function setContainer(ContainerInterface $container = null)
	{
		$this->container = $container;
	}

	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		/* Benutzer 1 */
		$user = new User();

		$user->setUsername("maltehuebner");
		$user->setEmail("maltehuebner@gmx.org");

		$encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
		$user->setPassword($encoder->encodePassword('secret', $user->getSalt()));

		$manager->persist($user);
		$manager->flush();


		/* Benutzer 2 */
		$user = new User();

		$user->setUsername("TestUser1");
		$user->setEmail("testuser1@criticalmass.in");

		$encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
		$user->setPassword($encoder->encodePassword('secret', $user->getSalt()));

		$manager->persist($user);
		$manager->flush();


		/* Benutzer 3 */
		$user = new User();

		$user->setUsername("TestUser2");
		$user->setEmail("testuser2@criticalmass.in");

		$encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
		$user->setPassword($encoder->encodePassword('secret', $user->getSalt()));

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
