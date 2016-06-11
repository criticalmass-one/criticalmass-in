<?php

namespace Caldera\Bundle\CalderaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class FrontpageController extends Controller
{
    public function indexAction()
    {
        return $this->render('CalderaBundle:Frontpage:index.html.twig');
    }
}
