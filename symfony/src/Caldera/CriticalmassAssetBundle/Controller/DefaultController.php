<?php

namespace Caldera\CriticalmassAssetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CalderaCriticalmassAssetBundle:Default:index.html.twig', array('name' => $name));
    }
}
