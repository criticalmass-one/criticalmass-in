<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;

class FrontpageController extends AbstractController
{
    public function indexAction()
    {
        $blogArticles = $this->getBlogArticleRepository()->findBy([]);
        $currentRides = $this->getRideRepository()->findFrontpageRides();
        
        return $this->render('CalderaCriticalmassSiteBundle:Frontpage:index.html.twig',
            [
                'blogArticles' => $blogArticles,
                'currentRides' => $currentRides
            ]);
    }
}
