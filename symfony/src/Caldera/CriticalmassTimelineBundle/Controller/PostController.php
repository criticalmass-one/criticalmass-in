<?php

namespace Caldera\CriticalmassTimelineBundle\Controller;

use Caldera\CriticalmassTimelineBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PostController extends Controller
{
    public function listAction(Request $request, $cityId = null, $rideId = null)
    {
        $criteria = array('enabled' => true);

        if ($cityId)
        {
            $criteria['city'] = $cityId;
        }

        if ($rideId)
        {
            $criteria['ride'] = $rideId;
        }

        $posts = $this->getDoctrine()->getRepository('CalderaCriticalmassTimelineBundle:Post')->findBy($criteria, array('dateTime' => 'DESC'));

        return $this->render('CalderaCriticalmassTimelineBundle:Post:list.html.twig', array('posts' => $posts));
    }
}
