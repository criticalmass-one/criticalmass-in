<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\RideType;
use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Symfony\Component\Form\Form;
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
            array(
                'city' => $city,
                'event' => $event,
                'photoCounter' => $photoCounter,
                'postCounter' => $postCounter
            )
        );
    }
}
