<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caldera\CriticalmassBundle\Entity\Comment;

class CommentController extends Controller
{
	public function listcommentsAction($citySlug)
	{
		$citySlug = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:CitySlug')->findOneBySlug($citySlug);

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
			$em = $this->getDoctrine()->getManager()->persist($comment);
		}

		return $this->redirect($this->generateUrl('caldera_criticalmass_listcomments', array('citySlug' => 'hamburg')));
	}
}
