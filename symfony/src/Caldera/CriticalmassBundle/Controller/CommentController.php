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
		
		$comments = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:Comment')->findAll();

	  $form = $this->createFormBuilder(new Comment())->add('text', 'text')->getForm();

		return $this->render('CalderaCriticalmassBundle:RideComments:list.html.twig', array('comments' => $comments, 'form' => $form->createView()));
	}
}
