<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LiveController extends Controller
{
	public function showAction($citySlug)
	{
		$city = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:CitySlug')->findOneBySlug($citySlug)->getCity();

		return $this->render('CalderaCriticalmassBundle:Live:show.html.twig', array('city' => $city));
	}
}
