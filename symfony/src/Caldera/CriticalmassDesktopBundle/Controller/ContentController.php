<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContentController extends Controller
{
    public function showAction($classSlug, $itemSlug)
    {
        $contentClass = $this->getDoctrine()->getRepository('CalderaCriticalmassContentBundle:ContentClass')->findOneBy(array('slug' => $classSlug));
        $contentItem = $this->getDoctrine()->getRepository('CalderaCriticalmassContentBundle:ContentItem')->findOneBy(array('contentClass' => $contentClass->getId()));

        return $this->render('CalderaCriticalmassDesktopBundle:Content:show.html.twig', array('contentItem' => $contentItem));
    }
}
