<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\BaseTrait\ViewStorageTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EventController extends AbstractController
{
    use ViewStorageTrait;

    public function showAction(Request $request, $citySlug, $eventSlug)
    {
        $city = $this->getCheckedCity($citySlug);

        $event = $this->getEventRepository()->findEventByCityAndSlug($city, $eventSlug);

        if (!$event) {
            throw new NotFoundHttpException('Dieses Event gibt es leider nicht :(');
        }

        $this->countEventView($event);

        $photoCounter = $this->getPhotoRepository()->countPhotosByEvent($event);
        $postCounter = $this->getPostRepository()->countPostsForEvent($event);

        return $this->render(
            'CalderaCriticalmassCoreBundle:Event:show.html.twig',
            [
                'city' => $city,
                'event' => $event,
                'photoCounter' => $photoCounter,
                'postCounter' => $postCounter
            ]
        );
    }
}
