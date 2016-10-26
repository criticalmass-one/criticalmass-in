<?php

namespace Caldera\Bundle\CriticalmassTipsBundle\Controller;

use Caldera\Bundle\CalderaBundle\Manager\ContentManager\ContentManager;
use Caldera\Bundle\CriticalmassCoreBundle\BaseTrait\ViewStorageTrait;
use Caldera\Bundle\CriticalmassSiteBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    use ViewStorageTrait;

    public function indexAction(Request $request, string $citySlug): Response
    {
        /** @var ContentManager $contentManager */
        $contentManager = $this->get('caldera.manager.content_manager');

        $content = $contentManager->getBySlug('tipps-fuer-die-teilnahme-in-hamburg');

        $this->countView($content);
        
        $this->getMetadata()
            ->setDescription('Kurz und knapp in drei Minuten: Tipps und Wissenswertes zur Teilnahme an der Critical Mass.')
            ->setKeywords('Critical Mass, Tipps, Corken');
        
        return $this->render(
            'CalderaCriticalmassTipsBundle:Default:index.html.twig',
            [
                'content' => $content
            ]
        );
    }
}
