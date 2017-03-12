<?php

namespace AppBundle\Controller\Photo;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Event;
use AppBundle\Entity\Photo;
use AppBundle\Entity\Ride;
use Symfony\Component\HttpFoundation\Request;

class PhotoGalleryController extends AbstractController
{
    public function galleryAction(Request $request, $citySlug, $rideDate = null, $eventSlug = null)
    {
        /** @var Ride $ride */
        $ride = null;

        /** @var Event $event */
        $event = null;

        if ($rideDate) {
            $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

            if ($ride && $ride->getRestrictedPhotoAccess() && !$this->getUser()) {
                throw $this->createAccessDeniedException();
            }

            $query = $this->getPhotoRepository()->buildQueryPhotosByRide($ride);
        } else {
            $event = $this->getEventRepository()->findOneBySlug($eventSlug);

            $query = $this->getPhotoRepository()->buildQueryPhotosByEvent($event);
        }

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            32
        );

        return $this->render(
            'AppBundle:PhotoGallery:gallerylist.html.twig',
            [
                'ride' => $ride,
                'event' => $event,
                'pagination' => $pagination
            ]
        );
    }

    public function ridegallerylistAction(Request $request)
    {

    }

    public function userlistAction(Request $request)
    {
        $result = $this->getPhotoRepository()->findRidesWithPhotoCounterByUser($this->getUser());

        return $this->render('AppBundle:Photo:userlist.html.twig',
            [
                'result' => $result
            ]
        );
    }

    public function examplegalleryAction(Request $request)
    {
        $photos = $this->getPhotoRepository()->findSomePhotos(32);

        $cityList = [];

        /**
         * @var Photo $photo
         */
        foreach ($photos as $photo) {
            $city = $photo->getRide()->getCity();
            $citySlug = $city->getSlug();

            $cityList[$citySlug] = $city;
        }

        shuffle($cityList);

        return $this->render(
            'AppBundle:PhotoGallery:examplegallery.html.twig',
            [
                'photos' => $photos,
                'cities' => $cityList
            ]
        );
    }
}
