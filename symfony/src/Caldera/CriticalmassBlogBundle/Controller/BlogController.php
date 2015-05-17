<?php

namespace Caldera\CriticalmassBlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function listAction(Request $request)
    {
        $articles = $this->getDoctrine()->getRepository('CalderaCriticalmassBlogBundle:Article')->findAll();

        $markdown = new Markdown();
        
        foreach ($articles as $article)
        {
            $article->setFormattedText($markdown->transform($article->getText()));
        }

        return $this->render('CalderaCriticalmassBlogBundle:Content:list.html.twig', array('articles' => $articles));
    }
}
