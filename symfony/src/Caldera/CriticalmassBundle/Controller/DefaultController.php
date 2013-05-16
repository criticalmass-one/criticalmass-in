<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
	public function showcityAction($city)
	{
		$city = $this->getDoctrine()->getRepository('CalderaCriticalmassBundle:City')->findOneByCity($city);

		if (empty($city))
		{
			throw $this->createNotFoundException('This city does not exist');
		}
		else
		{
			return $this->render('CalderaCriticalmassBundle:Default:index.html.twig', array('city' => $city));
		}
	}
}
