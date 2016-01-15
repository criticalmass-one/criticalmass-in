<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassSiteBundle\Controller\AbstractController;
use Michelf\Markdown;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends AbstractController
{
    public function listAction(Request $request)
    {
        $articles = $this->getBlogArticleRepository()->findBy(array(), array('dateTime' => 'DESC'));

        $markdown = new Markdown();
        
        foreach ($articles as $article)
        {
            $article->setFormattedText($markdown->transform($article->getText()));
        }

        return $this->render('CalderaCriticalmassSiteBundle:Blog:list.html.twig', array('articles' => $articles));
    }
}
