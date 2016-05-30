<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\BaseTrait\ViewStorageTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventController extends AbstractController
{
    public function showAction(Request $request, $citySlug, $eventSlug)
    {
        $city = $this->getCheckedCity($citySlug);

        $event = $this->getEventRepository()->findEventByCityAndSlug($city, $eventSlug);

        if (!$event) {
            throw new NotFoundHttpException('Dieses Event gibt es leider nicht :(');
        }

        $photoCounter = $this->getPhotoRepository()->countPhotosByEvent($event);
        $postCounter = $this->getPostRepository()->countPostsForEvent($event);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Event:show.html.twig',
            [
                'city' => $city,
                'event' => $event,
                'photoCounter' => $photoCounter,
                'postCounter' => $postCounter
            ]
        );
    }
}
