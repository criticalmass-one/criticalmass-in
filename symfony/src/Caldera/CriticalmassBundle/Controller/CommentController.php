<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CommentController extends Controller
{
	public function listcommentsAction($citySlug)
	{
		$citySlug = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:CitySlug')->findOneBySlug($citySlug);

		$ride = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:Ride')->findOneBy(array('city_id' => $citySlug->getCity()->getId()), array('date' => 'desc'));
		
		$comments = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:Comment')->findByRide($ride);

		return $this->render('CalderaCriticalmassBundle:RideComments:list.html.twig', array('comments' => $comments));
	}
}
