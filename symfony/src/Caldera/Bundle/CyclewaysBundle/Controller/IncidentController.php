<?php

namespace Caldera\Bundle\CyclewaysBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MapController extends Controller
{
    public function mapAction()
    {
        return $this->render('CalderaCyclewaysBundle:Frontpage:index.html.twig');
    }
}
