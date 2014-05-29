<?php

namespace Caldera\CriticalmassMobileBundle\Controller;

use Caldera\CriticalmassStatisticBundle\Utility\Trackable;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MultiPageController extends Controller
{
    public function mainAction()
    {
        return $this->render('CalderaCriticalmassMobileBundle:MultiPage:main.html.twig', array('citySlug' => 'hamburg'));
    }

    public function slugindexAction($slug)
    {
        return $this->render('CalderaCriticalmassMobileBundle:MultiPage:main.html.twig', array('citySlug' => $slug));
    }
}
