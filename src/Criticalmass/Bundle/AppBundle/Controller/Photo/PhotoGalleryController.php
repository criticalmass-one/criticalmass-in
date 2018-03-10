<?php

namespace Criticalmass\Bundle\AppBundle\Controller\Photo;

use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\Photo;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class PhotoGalleryController extends AbstractController
{
    public function galleryAction(Request $request, PaginatorInterface $paginator, string $citySlug, string $rideDate): Response
    {
        /** @var Ride $ride */
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        if ($ride && $ride->getRestrictedPhotoAccess() && !$this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $query = $this->getPhotoRepository()->buildQueryPhotosByRide($ride);

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            32
        );

        return $this->render(
            'AppBundle:PhotoGallery:gallery_list.html.twig',
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
            'AppBundle:PhotoGallery:user_list.html.twig',
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
            'AppBundle:PhotoGallery:example_gallery.html.twig',
            [
                'photos' => $photos,
                'cities' => $cityList,
            ]
        );
    }
}
