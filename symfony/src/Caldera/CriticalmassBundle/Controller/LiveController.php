<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class LiveController extends Controller
{
	public function showAction($page)
	{
		return $this->render('CalderaCriticalmassBundle:Live:show.html.twig');
	}
}
