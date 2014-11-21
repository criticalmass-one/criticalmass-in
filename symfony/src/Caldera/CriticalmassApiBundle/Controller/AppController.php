<?php

namespace Caldera\CriticalmassApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Caldera\CriticalmassCoreBundle\Utility as Utility;
use Caldera\CriticalmassCoreBundle\Entity as Entity;

class AppController extends Controller
{
    public function listAction()
    {
        $apps = $this->getDoctrine()->getRepository('CalderaCriticalmassApiBundle:App')->findBy(array('user' => $this->getUser()->getId()));

        return $this->render('CalderaCriticalmassApiBundle:App:list.html.twig', array('apps' => $apps));
    }
}
