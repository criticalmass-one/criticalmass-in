<?php

namespace Caldera\Bundle\CriticalmassTipsBundle\Controller;

use Caldera\Bundle\CriticalmassSiteBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{
    public function indexAction(Request $request)
    {
        return $this->render('CalderaCriticalmassTipsBundle:Default:index.html.twig');
    }
}
