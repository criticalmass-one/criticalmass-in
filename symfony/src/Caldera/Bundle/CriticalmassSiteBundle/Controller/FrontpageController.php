<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

class FrontpageController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $blogArticles = $this->getBlogArticleRepository()->findBy([]);
        $currentRides = $this->getRideRepository()->findCurrentRides();

        return $this->render(
            'CalderaCriticalmassSiteBundle:Frontpage:index.html.twig',
            [
                'blogArticles' => $blogArticles,
                'currentRides' => $currentRides
            ]
        );
    }
}
