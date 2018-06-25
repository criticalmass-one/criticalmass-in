<?php

namespace AppBundle\Controller\Photo;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\City;
use AppBundle\Entity\Photo;
use AppBundle\Entity\Ride;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PhotoGalleryController extends AbstractController
{
    /**
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function galleryAction(Request $request, PaginatorInterface $paginator, Ride $ride): Response
    {
        $this->errorIfFeatureDisabled('photos');

        if ($ride && $ride->getRestrictedPhotoAccess() && !$this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $query = $this->getPhotoRepository()->buildQueryPhotosByRide($ride);

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            32
        );

        return $this->render('AppBundle:PhotoGallery:gallery_list.html.twig', [
            'ride' => $ride,
            'pagination' => $pagination,
        ]);
    }

    public function userlistAction(Request $request, UserInterface $user): Response
    {
        $this->errorIfFeatureDisabled('photos');

        $result = $this->getPhotoRepository()->findRidesWithPhotoCounterByUser($user);

        return $this->render('AppBundle:PhotoGallery:user_list.html.twig', [
            'result' => $result,
        ]);
    }

    public function examplegalleryAction(): Response
    {
        $this->errorIfFeatureDisabled('photos');

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

        return $this->render('AppBundle:PhotoGallery:example_gallery.html.twig', [
            'photos' => $photos,
            'cities' => $cityList,
        ]);
    }
}
