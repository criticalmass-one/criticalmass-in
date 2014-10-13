<?php

namespace Caldera\CriticalmassGalleryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CriticalmassGalleryBundle:Default:index.html.twig', array('name' => $name));
    }
}
