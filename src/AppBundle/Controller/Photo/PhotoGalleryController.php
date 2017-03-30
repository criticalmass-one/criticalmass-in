<?php

namespace AppBundle\Controller\Photo;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\City;
use AppBundle\Entity\Photo;
use AppBundle\Entity\Ride;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class PhotoGalleryController extends AbstractController
{
    public function galleryAction(Request $request, string $citySlug, string $rideDate): Response
    {
        /** @var Ride $ride */
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        if ($ride && $ride->getRestrictedPhotoAccess() && !$this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $query = $this->getPhotoRepository()->buildQueryPhotosByRide($ride);

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
                'pagination' => $pagination,
            ]
        );
    }

    public function userlistAction(Request $request, UserInterface $user): Response
    {
        $result = $this->getPhotoRepository()->findRidesWithPhotoCounterByUser($user);

        return $this->render(
            'AppBundle:Photo:userlist.html.twig',
            [
                'result' => $result,
            ]
        );
    }

    public function examplegalleryAction(Request $request): Response
    {
        $photos = $this->getPhotoRepository()->findSomePhotos(32);

        $cityList = [];

        /** @var Photo $photo */
        foreach ($photos as $photo) {
            /** @var City $city */
            $city = $photo->getRide()->getCity();
            $citySlug = $city->getSlug();

            $cityList[$citySlug] = $city;
        }

        shuffle($cityList);

        return $this->render(
            'AppBundle:PhotoGallery:examplegallery.html.twig',
            [
                'photos' => $photos,
                'cities' => $cityList,
            ]
        );
    }
}
