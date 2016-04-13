<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ChatController extends AbstractController
{
    public function indexAction(Request $request)
    {
        return $this->render(
            'CalderaCriticalmassSiteBundle:Chat:index.html.twig',
            [
            ]
        );
    }
}
