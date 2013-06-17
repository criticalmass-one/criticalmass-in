<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caldera\CriticalmassBundle\Entity\Comment;

class CommentController extends Controller
{
	public function listcommentsAction($citySlug)
	{
		$citySlug = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:CitySlug')->findOneBySlug($citySlug);

		$comments = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:Comment')->findAll();

		$form = $this->createFormBuilder(new Comment())->add('text', 'text')->getForm();


		$ride = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:Ride')->findOneBy(array('city_id' => $citySlug->getCity()->getId()), array('date' => 'desc'));
		
		return $this->render('CalderaCriticalmassBundle:RideComments:list.html.twig', array('comments' => $comments, 'form' => $form->createView()));
	}

	public function addcommentAction($citySlug)
	{
		$citySlug = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:CitySlug')->findOneBySlug($citySlug);

		$ride = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:Ride')->findOneBy(array('city_id' => $citySlug->getCity()->getId()), array('date' => 'desc'));

		$comment = new Comment();
		$form = $this->createFormBuilder($comment)->add('text', 'text')->getForm();

		$form->handleRequest($this->getRequest());

		if ($form->isValid())
		{
			$comment->setUser($this->getDoctrine()->getRepository('CalderaCriticalmassBundle:User')->findOneByUsername("maltehuebner"));
			$comment->setRide($ride);
			$comment->setCreationDateTime(new \DateTime("2013-06-17 23:51:55"));

			$em = $this->getDoctrine()->getManager();
			$em->persist($comment);
			$em->flush();
		}

		return $this->redirect($this->generateUrl('caldera_criticalmass_listcomments', array('citySlug' => 'hamburg')));
	}
}
