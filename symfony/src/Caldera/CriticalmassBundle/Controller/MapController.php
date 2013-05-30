<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Caldera\CriticalmassBundle\Utility as Utility;
use Caldera\CriticalmassBundle\Entity as Entity;

class MapController extends Controller
{
	public function mapdataAction()
	{
		$mph = new Utility\MapPositionHandler(
			new Entity\Ride(),
			$this->getDoctrine()->getRepository('CalderaCriticalmassBundle:Position')->findBy(array(), array("id" => "DESC"), 100)
	);

		$response = new Response();
		$response->setContent(json_encode(array(
			'mapcenter' => array(
				'latitude' => $mph->getMapCenterLatitude(),
				'longitude' => $mph->getMapCenterLongitude()
			),
			'zoom' => $mph->getZoomFactor(),
			'positions' => $mph->getPositionArray()
		)));

		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}
}
