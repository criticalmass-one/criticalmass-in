<?php

namespace Caldera\CriticalmassBlogBundle\Controller;

use Michelf\Markdown;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends Controller
{
    public function listAction(Request $request)
    {
        $articles = $this->getDoctrine()->getRepository('CalderaCriticalmassBlogBundle:Article')->findBy(array(), array('dateTime' => 'DESC'));

        $markdown = new Markdown();
        
        foreach ($articles as $article)
        {
            $article->setFormattedText($markdown->transform($article->getText()));
        }

        return $this->render('CalderaCriticalmassBlogBundle:Blog:list.html.twig', array('articles' => $articles));
    }
}
