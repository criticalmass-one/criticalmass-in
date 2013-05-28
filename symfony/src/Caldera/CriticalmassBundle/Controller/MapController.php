<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Caldera\CriticalmassBundle\Utility as Utility;
use Caldera\CriticalmassBundle\Entity as Entity;

class MapController extends Controller
{
	public function mapcenterAction()
	{
		$mph = new Utility\MapPositionHandler(new Entity\Ride());

		$response = new Response();
		$response->setContent(json_encode(array(
			'mapcenter' => $mph->getMapCenter(),
			'zoom' => $mph->getZoomFactor(),
			'positions' => $mph->getPositions()
		)));

		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}
}
