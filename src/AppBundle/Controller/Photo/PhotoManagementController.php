<?php

namespace AppBundle\Controller\Photo;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Photo;
use AppBundle\Form\Type\PhotoCoordType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\User\UserInterface;

class PhotoManagementController extends AbstractController
{
    public function listAction(Request $request, UserInterface $user): Response
    {
        $result = $this->getPhotoRepository()->findRidesWithPhotoCounterByUser($user);

        return $this->render(
            'AppBundle:PhotoManagement:userlist.html.twig',
            [
                'result' => $result
            ]
        );
    }

    public function ridelistAction(Request $request, $citySlug, $rideDate): Response
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $query = $this->getPhotoRepository()->buildQueryPhotosByRide($ride);

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            32
        );

        return $this->render(
            'AppBundle:PhotoManagement:ridelist.html.twig',
            [
                'ride' => $ride,
                'pagination' => $pagination
            ]
        );
    }

    public function deleteAction(Request $request, $citySlug, $rideDate, $photoId = 0): Response
    {
        /**
         * @var Photo $photo
         */
        $photo = $this->getPhotoByIdCitySlugRideDate($citySlug, $rideDate, $photoId);

        if ($photo) {
            $em = $this->getDoctrine()->getManager();

            $photo->setDeleted(true);

            $em->persist($photo);
            $em->flush();
        }

        return $this->redirect($this->getRedirectManagementPageUrl($request));
    }

    public function manageAction(Request $request, $citySlug, $rideDate): Response
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $query = $this->getPhotoRepository()->buildQueryPhotosByUserAndRide($this->getUser(), $ride);

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            32
        );

        return $this->render('AppBundle:PhotoManagement:manage.html.twig',
            [
                'ride' => $ride,
                'pagination' => $pagination
            ]
        );
    }

    public function toggleAction(Request $request, $citySlug, $rideDate, $photoId): Response
    {
        /**
         * @var Photo $photo
         */
        $photo = $this->getPhotoByIdCitySlugRideDate($citySlug, $rideDate, $photoId);

        if ($photo) {
            $em = $this->getDoctrine()->getManager();

            $photo->setEnabled(!$photo->getEnabled());

            $em->persist($photo);
            $em->flush();
        }

        return $this->redirect($this->getRedirectManagementPageUrl($request));
    }

    public function featuredPhotoAction(Request $request, $citySlug, $rideDate, $photoId): Response
    {
        /**
         * @var Photo $photo
         */
        $photo = $this->getPhotoByIdCitySlugRideDate($citySlug, $rideDate, $photoId);

        $photo->getRide()->setFeaturedPhoto($photo);

        $em = $this->getDoctrine()->getManager();
        $em->persist($photo->getRide());
        $em->flush();

        return $this->redirect($this->getRedirectManagementPageUrl($request));
    }


    protected function getPhotoByIdCitySlugRideDate($citySlug, $rideDate, $photoId): Response
    {
        /**
         * @var Photo $photo
         */
        $photo = $this->getPhotoRepository()->find($photoId);

        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);
        $photo = $this->getPhotoRepository()->find($photoId);

        if ($ride &&
            $photo &&
            $photo->getUser()->equals($this->getUser()) &&
            $photo->getRide()->equals($ride)
        ) {
            return $photo;
        }

        return null;
    }

    public function placeSingleAction(Request $request, $citySlug, $rideDate, $photoId): Response
    {
        /**
         * @var Photo $photo
         */
        $photo = $this->getPhotoByIdCitySlugRideDate($citySlug, $rideDate, $photoId);

        if ($photo) {
            $form = $this->createForm(
                PhotoCoordType::class,
                $photo,
                [
                    'action' => $this->generateUrl('caldera_criticalmass_photo_place_single',
                        [
                            'citySlug' => $citySlug,
                            'rideDate' => $rideDate,
                            'photoId' => $photoId
                        ]
                    )
                ]
            );

            if ('POST' == $request->getMethod()) {
                return $this->placeSinglePostAction($request, $photo, $form);
            } else {
                return $this->placeSingleGetAction($request, $photo, $form);
            }
        } else {
            throw new NotFoundHttpException();
        }
    }

    protected function placeSingleGetAction(Request $request, Photo $photo, Form $form): Response
    {
        $previousPhoto = $this->getPhotoRepository()->getPreviousPhoto($photo);
        $nextPhoto = $this->getPhotoRepository()->getNextPhoto($photo);

        $track = $this->getTrackRepository()->findByUserAndRide($photo->getRide(), $this->getUser());

        return $this->render('AppBundle:PhotoManagement:place.html.twig',
            [
                'photo' => $photo,
                'previousPhoto' => $previousPhoto,
                'nextPhoto' => $nextPhoto,
                'track' => $track,
                'form' => $form->createView()
            ]
        );
    }

    protected function placeSinglePostAction(Request $request, Photo $photo, Form $form): Response
    {
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
        }

        return $this->redirect($this->getRedirectManagementPageUrl($request));
    }

    public function relocateAction(Request $request, $citySlug, $rideDate): Response
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $photos = $this->getPhotoRepository()->findPhotosByUserAndRide($this->getUser(), $ride);

        $track = $this->getTrackRepository()->findByUserAndRide($ride, $this->getUser());

        return $this->render('AppBundle:PhotoManagement:relocate.html.twig',
            [
                'ride' => $ride,
                'photos' => $photos,
                'track' => $track
            ]
        );
    }

    public function rotateAction(Request $request, $citySlug, $rideDate, $photoId): Response
    {
        /**
         * @var Photo $photo
         */
        $photo = $this->getPhotoByIdCitySlugRideDate($citySlug, $rideDate, $photoId);

        $rotate = 90;

        if ($request->query->get('rotate') && $request->query->get('rotate') == 'right') {
            $rotate = -90;
        }

        $path = $this->getParameter('kernel.root_dir') . '/../web';
        $filename = $this->get('vich_uploader.templating.helper.uploader_helper')->asset($photo, 'imageFile');

        $image = imagecreatefromjpeg($path . $filename);
        $image = imagerotate($image, $rotate, 0);
        imagejpeg($image, $path . $filename, 100);
        imagedestroy($image);

        $this->recachePhoto($photo);

        return $this->redirect($this->getRedirectManagementPageUrl($request));
    }

    protected function recachePhoto(Photo $photo): void
    {
        $filename = $this->get('vich_uploader.templating.helper.uploader_helper')->asset($photo, 'imageFile');

        $imagineCache = $this->get('liip_imagine.cache.manager');
        $imagineCache->remove($filename);

        $imagineController = $this->get('liip_imagine.controller');
        $imagineController->filterAction(new Request(), $filename, 'gallery_photo_thumb');
        $imagineController->filterAction(new Request(), $filename, 'gallery_photo_standard');
        $imagineController->filterAction(new Request(), $filename, 'gallery_photo_large');
        $imagineController->filterAction(new Request(), $filename, 'city_image_wide');
    }

    protected function getRedirectManagementPageUrl(Request $request): string
    {
        $referer = $request->headers->get('referer');

        return $referer;
    }
}
