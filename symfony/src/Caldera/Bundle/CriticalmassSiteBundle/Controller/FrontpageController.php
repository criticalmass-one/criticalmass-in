<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Symfony\Component\Security\Core\SecurityContext;

class FrontpageController extends AbstractController
{
    public function indexAction()
    {
        $dateTime = new \DateTime();
        
        $blogArticles = $this->getBlogArticleRepository()->findBy(array());
        $carouselRides = $this->getRideRepository()->findRecentRides(null, null, 5, 1000);
        
        return $this->render('CalderaCriticalmassSiteBundle:Frontpage:index.html.twig',
            [
                'blogArticles' => $blogArticles,
                'carouselRides' => $carouselRides
            ]);
    }
}
