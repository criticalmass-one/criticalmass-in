<?php

namespace Caldera\Bundle\CriticalmassOneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction(Request $request)
    {
        return $this->render('CalderaCriticalmassOneBundle:Default:index.html.twig');
    }
}
