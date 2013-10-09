<?php

namespace Caldera\CriticalmassCoreBundle\DataFixtures\ORM;

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
		$user->setEnabled(true);
		$user->setSendGPSInformation(true);
		$user->setGPSInterval(5);

		$encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
		$user->setPassword($encoder->encodePassword('KennwortMalte', $user->getSalt()));

		$manager->persist($user);
		$manager->flush();

		$this->addReference("user-maltehuebner", $user);


		/* Benutzer 2 */
		$user = new User();

		$user->setUsername("TestUser1");
		$user->setEmail("testuser1@criticalmass.in");
		$user->setEnabled(true);
		$user->setSendGPSInformation(true);
		$user->setGPSInterval(5);

		$encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
		$user->setPassword($encoder->encodePassword('KennwortTest1', $user->getSalt()));

		$manager->persist($user);
		$manager->flush();

		$this->addReference("user-testuser1", $user);


		/* Benutzer 3 */
		$user = new User();

		$user->setUsername("TestUser2");
		$user->setEmail("testuser2@criticalmass.in");
		$user->setEnabled(true);
		$user->setSendGPSInformation(true);
		$user->setGPSInterval(5);

		$encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
		$user->setPassword($encoder->encodePassword('KennwortTest2', $user->getSalt()));

		$manager->persist($user);
		$manager->flush();

		$this->addReference("user-testuser2", $user);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getOrder()
	{
		return 3;
	}
}
