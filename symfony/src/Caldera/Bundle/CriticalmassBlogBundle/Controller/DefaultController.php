<?php

namespace Caldera\Bundle\CriticalmassBlogBundle\Controller;

use Caldera\Bundle\CriticalmassSiteBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $posts = $this->getBlogPostRepository()->findAll();

        return $this->render(
            'CalderaCriticalmassBlogBundle:Default:index.html.twig',
            [
                'posts' => $posts
            ]
        );
    }
}
