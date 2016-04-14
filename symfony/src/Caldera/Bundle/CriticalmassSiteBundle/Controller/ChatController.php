<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ChatController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $recentMessages = $this->getPostRepository()->findRecentChatMessages();

        $anonymousName = null;

        if (!$this->getUser()) {
            $anonymousName = $this->getAnonymousNameRepository()->findOneRandomUnusedName();
        }

        return $this->render(
            'CalderaCriticalmassSiteBundle:Chat:index.html.twig',
            [
                'recentMessages' => $recentMessages,
                'anonymousName' => $anonymousName
            ]
        );
    }
}
