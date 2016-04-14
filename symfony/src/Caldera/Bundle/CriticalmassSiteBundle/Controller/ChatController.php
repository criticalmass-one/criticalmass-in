<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ChatController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $recentMessages = $this->getPostRepository()->findRecentChatMessages();
        
        if (!$this->getUser() and !$this->getSession()->get('anonymousName')) {
            $anonymousName = $this->getAnonymousNameRepository()->findOneRandomUnusedName();

            $this->getSession()->set('anonymousName', $anonymousName);
        } elseif (!$this->getUser() and $this->getSession()->get('anonymousName')) {
            $anonymousName = $this->getSession()->get('anonymousName');
        } else {
            $anonymousName = null;
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
