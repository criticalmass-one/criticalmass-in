<?php

namespace Caldera\CriticalmassBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Caldera\CriticalmassBundle\Entity\Comment;

class LoadCommentData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$comment = new Comment();
		$comment->setUser($this->getReference("user-maltehuebner"));
		$comment->setRide($this->getReference("city-hamburg-ride-2013-05-31"));
		$comment->setCreationDateTime(new \DateTime("2013-05-31 18:50:12"));
		$comment->setText("Ich freu mich schon mega!");

		$manager->persist($comment);
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