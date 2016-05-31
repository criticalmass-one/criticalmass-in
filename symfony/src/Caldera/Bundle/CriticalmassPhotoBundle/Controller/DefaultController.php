<?php

namespace Caldera\Bundle\CriticalmassPhotoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalderaCriticalmassPhotoBundle:Default:index.html.twig', array('name' => $name));
    }
}
