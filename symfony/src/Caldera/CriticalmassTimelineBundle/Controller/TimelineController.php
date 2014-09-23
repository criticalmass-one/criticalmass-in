<?php

namespace Caldera\CriticalmassTimelineBundle\Controller;

use Caldera\CriticalmassTimelineBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TimelineController extends Controller
{
    public function listAction(Request $request)
    {
        $posts = $this->getDoctrine()->getRepository('CalderaCriticalmassTimelineBundle:Post')->findBy(array('enabled' => true), array('dateTime' => 'DESC'));

        return $this->render('CalderaCriticalmassTimelineBundle:Timeline:list.html.twig', array('posts' => $posts));
    }
}
