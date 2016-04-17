<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassModelBundle\Entity\AnonymousName;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ChatController extends AbstractController
{
    public function indexAction(Request $request)
    {
        $recentMessages = $this->getPostRepository()->findRecentChatMessages();

        $anonymousName = null;

        if (!$this->getUser()) {
            $anonymousName = $this->getSession()->get('anonymousName');
        }

        return $this->render(
            'CalderaCriticalmassSiteBundle:Chat:index.html.twig',
            [
                'recentMessages' => $recentMessages,
                'anonymousName' => $anonymousName ? $anonymousName->getName() : null,
                'anonymousNameId' => $anonymousName ? $anonymousName->getId() : null
            ]
        );
    }

    public function createAnonymousNameAction(Request $request)
    {
        // @todo get better genderlist

        $genderList = [
            'male' => 'male',
            'female' => 'female',
            'transgender' => null,
            'genderless' => null
        ];

        if (!$request->get('gender')) {
            throw new NotFoundHttpException();
        }

        $gender = $genderList[$request->get('gender')];

        /**
         * @var AnonymousName $anonymousName
         */
        $anonymousName = $this->getAnonymousNameRepository()->findOneRandomUnusedName($gender);

        $this->getSession()->set('anonymousName', $anonymousName);

        $response = new JsonResponse();
        $response->setData([
            'anonymousName' => $anonymousName->getName(),
            'anonymousNameId' => $anonymousName->getId()
        ]);

        return $response;
    }
}
