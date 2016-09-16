<?php

namespace Caldera\Bundle\CriticalmassLiveBundle\Controller;

use Caldera\Bundle\CalderaBundle\Entity\Content;
use Caldera\Bundle\CriticalmassCoreBundle\BaseTrait\ViewStorageTrait;
use Caldera\Bundle\CriticalmassSiteBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ContentController extends AbstractController
{
    use ViewStorageTrait;
    
    public function showAction(Request $request, string $contentSlug): Response
    {
        /** @var Content $content */
        $content = $this->getContentRepository()->findBySlug($contentSlug);

        if (!$content) {
            throw new NotFoundHttpException('Schade, unter dem Stichwort ' . $contentSlug . ' wurde kein Inhalt hinterlegt.');
        }

        $this->countContentView($content);

        return $this->render(
            'CalderaCriticalmassLiveBundle:Content:show.html.twig',
            [
                'content' => $content
            ]
        );
    }
}
