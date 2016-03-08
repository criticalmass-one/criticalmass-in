<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\PhotoCoordType;
use Caldera\Bundle\CriticalmassCoreBundle\Image\ExifReader\DateTimeExifReader;
use Caldera\Bundle\CriticalmassCoreBundle\Image\PhotoGps\PhotoGps;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Photo;
use Caldera\Bundle\CriticalmassModelBundle\Entity\PhotoView;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PhotoGalleryController extends AbstractController
{
    public function galleryAction(Request $request, $citySlug, $rideDate = null, $eventSlug = null)
    {
        $ride = null;
        $event = null;

        if ($rideDate) {
            $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

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
            'CalderaCriticalmassSiteBundle:PhotoGallery:gallery.html.twig',
            [
                'ride' => $ride,
                'event' => $event,
                'pagination' => $pagination
            ]
        );
    }

    public function userlistAction(Request $request)
    {
        $result = $this->getPhotoRepository()->findRidesWithPhotoCounterByUser($this->getUser());

        return $this->render('CalderaCriticalmassSiteBundle:Photo:userlist.html.twig',
            [
                'result' => $result
            ]
        );
    }

    public function citygalleryAction(Request $request)
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
            'CalderaCriticalmassSiteBundle:Photo:citygallery.html.twig',
            [
                'photos' => $photos,
                'cities' => $cityList
            ]
        );
    }
}
