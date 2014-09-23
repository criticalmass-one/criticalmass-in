<?php

namespace Caldera\CriticalmassTimelineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TimelineController extends Controller
{
    public function listAction()
    {
        $posts = $this->getDoctrine()->getRepository('CalderaCriticalmassTimelineBundle:Post')->findBy(array('enabled' => true), array('dateTime' => 'DESC'));

        return $this->render('CalderaCriticalmassTimelineBundle:Timeline:list.html.twig', array('posts' => $posts));
    }
}
