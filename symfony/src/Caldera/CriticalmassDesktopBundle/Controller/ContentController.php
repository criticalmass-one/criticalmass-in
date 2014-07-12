<?php

namespace Caldera\CriticalmassDesktopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentController extends Controller
{
    public function showAction($classSlug, $itemSlug)
    {
        $contentClass = $this->getDoctrine()->getRepository('CalderaCriticalmassContentBundle:ContentClass')->findOneBy(array('slug' => $classSlug));
        $contentItem = $this->getDoctrine()->getRepository('CalderaCriticalmassContentBundle:ContentItem')->findOneBy(array('contentClass' => $contentClass->getId(), 'slug' => $itemSlug));

        if ($contentItem)
        {
            return $this->render('CalderaCriticalmassDesktopBundle:Content:show.html.twig', array('contentItem' => $contentItem));
        }
        else
        {
            throw new NotFoundHttpException('Diesen Inhalt gibt es nicht. Mehr wissen wir auch nicht :(');
        }
    }
}
