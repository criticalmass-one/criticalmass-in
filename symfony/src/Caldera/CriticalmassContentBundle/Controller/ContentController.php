<?php

namespace Caldera\CriticalmassContentBundle\Controller;

use Michelf\Markdown;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ContentController extends Controller
{
    public function showAction(Request $request, $slug)
    {
        $content = $this->getDoctrine()->getRepository('CalderaCriticalmassContentBundle:Content')->findBy(array('slug' => $slug, 'enabled' => true));
        
        $content = array_pop($content);
        
        $markdown = new Markdown();
        $parsedText = $markdown->transform($content->getText());
        
        return $this->render('CalderaCriticalmassContentBundle:Content:show.html.twig', array('content' => $content, 'parsedText' => $parsedText));
    }
}
