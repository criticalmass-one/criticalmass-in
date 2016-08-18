<?php

namespace Caldera\Bundle\CriticalmassLiveBundle\Controller;

use Caldera\Bundle\CriticalmassSiteBundle\Controller\AbstractController;

class LiveController extends AbstractController
{
    public function indexAction($name)
    {
        return $this->render('CalderaCriticalmassLiveBundle:Default:index.html.twig', array('name' => $name));
    }
}
