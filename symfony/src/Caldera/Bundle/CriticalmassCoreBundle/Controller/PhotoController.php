<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\Event;
use Caldera\Bundle\CalderaBundle\Entity\Photo;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\Track;
use Caldera\Bundle\CriticalmassCoreBundle\BaseTrait\ViewStorageTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PhotoController extends AbstractController
{
    use ViewStorageTrait;

    public function showAction(Request $request, $citySlug, $rideDate = null, $eventSlug = null, $photoId)
    {
        /** @var City $city */
        $city = $this->getCheckedCity($citySlug);

        /** @var Ride $ride */
        $ride = null;

        /** @var Event $event */
        $event = null;

        /** @var Track $track */
        $track = null;

        if ($rideDate) {
            $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);
        } else {
            $event = $this->getEventRepository()->findOneBySlug($eventSlug);
        }

        if ($ride && $ride->getRestrictedPhotoAccess() && !$this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        /** @var Photo $photo */
        $photo = $this->getPhotoRepository()->find($photoId);

        $previousPhoto = $this->getPhotoRepository()->getPreviousPhoto($photo);
        $nextPhoto = $this->getPhotoRepository()->getNextPhoto($photo);

        $this->countPhotoView($photo);

        if ($ride && $photo->getUser()) {
            /** @var Track $track */
            $track = $this->getTrackRepository()->findByUserAndRide($ride, $photo->getUser());
        }

        return $this->render('CalderaCriticalmassSiteBundle:Photo:show.html.twig',
            [
                'photo' => $photo,
                'nextPhoto' => $nextPhoto,
                'previousPhoto' => $previousPhoto,
                'city' => $city,
                'ride' => $ride,
                'event' => $event,
                'track' => $track
            ]
        );
    }

    /**
     * Trigger a photo view if the javascript gallery is used.
     *
     * @param Request $request
     * @return Response
     * @author maltehuebner
     * @since 2016
     */
    public function ajaxphotoviewAction(Request $request)
    {
        $photoId = $request->get('photoId');

        /**
         * @var Photo $photo
         */
        $photo = $this->getPhotoRepository()->find($photoId);

        if ($photo) {
            $this->countPhotoView($photo);
        }

        return new Response(null);
    }
}
