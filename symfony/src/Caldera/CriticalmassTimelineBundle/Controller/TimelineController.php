<?php

namespace Caldera\CriticalmassTimelineBundle\Controller;

use Caldera\CriticalmassTimelineBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TimelineController extends Controller
{
    public function listAction(Request $request, $page)
    {
        $pageLimit = 20;

        $posts = $this->getDoctrine()->getRepository('CalderaCriticalmassTimelineBundle:Post')->findBy(array('enabled' => true), array('dateTime' => 'DESC'), $pageLimit, ($page - 1) * $pageLimit);

        $postCount = $this->getDoctrine()->getRepository('CalderaCriticalmassTimelineBundle:Post')->countPosts();

        return $this->render('CalderaCriticalmassTimelineBundle:Timeline:list.html.twig', array('posts' => $posts, 'page' => $page, 'postCount' => $postCount));
    }
}
