<?php

namespace Caldera\CriticalmassBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class StaticPageController extends Controller
{
	public function showAction($page)
	{
		return $this->render('CalderaCriticalmassBundle:Static:'.$page.'.html.twig');
	}
}
