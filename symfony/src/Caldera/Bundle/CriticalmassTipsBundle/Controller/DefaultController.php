<?php

namespace Caldera\Bundle\CriticalmassTipsBundle\Controller;

use Caldera\Bundle\CalderaBundle\Manager\ContentManager\ContentManager;
use Caldera\Bundle\CriticalmassSiteBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{
    public function indexAction(Request $request)
    {
        /** @var ContentManager $contentManager */
        $contentManager = $this->get('caldera.manager.content_manager');

        $content = $contentManager->getBySlug('tips-fuer-die-teilnahme-in-hamburg');
        
        return $this->render('CalderaCriticalmassTipsBundle:Default:index.html.twig');
    }
}
