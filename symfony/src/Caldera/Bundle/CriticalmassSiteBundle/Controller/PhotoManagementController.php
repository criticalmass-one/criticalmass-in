<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CalderaBundle\Entity\Photo;
use Caldera\Bundle\CriticalmassCoreBundle\Form\Type\PhotoCoordType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PhotoManagementController extends AbstractController
{
    public function listAction(Request $request)
    {
        $result = $this->getPhotoRepository()->findRidesWithPhotoCounterByUser($this->getUser());

        return $this->render('CalderaCriticalmassSiteBundle:PhotoManagement:userlist.html.twig',
            [
                'result' => $result
            ]
        );
    }

    public function indexAction()
    {
        $criteria = array('enabled' => true);
        $photos = $this->getDoctrine()->getRepository('CalderaCriticalmassGalleryBundle:Photo')->findBy($criteria, array('dateTime' => 'DESC'));
        return $this->render('CalderaCriticalmassGalleryBundle:Default:list.html.twig', array('photos' => $photos));
    }

    public function ridelistAction(Request $request, $citySlug, $rideDate)
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
            'CalderaCriticalmassSiteBundle:PhotoManagement:ridelist.html.twig',
            [
                'ride' => $ride,
                'pagination' => $pagination
            ]
        );
    }

    public function showAction(Request $request, $citySlug, $rideDate, $photoId)
    {
        $city = $this->getCheckedCity($citySlug);
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        /**
         * @var Photo $photo
         */
        $photo = $this->getPhotoRepository()->find($photoId);

        $previousPhoto = $this->getPhotoRepository()->getPreviousPhoto($photo);
        $nextPhoto = $this->getPhotoRepository()->getNextPhoto($photo);

        $this->countView($photo);

        return $this->render('CalderaCriticalmassSiteBundle:PhotoManagement:show.html.twig',
            [
                'photo' => $photo,
                'nextPhoto' => $nextPhoto,
                'previousPhoto' => $previousPhoto,
                'city' => $city,
                'ride' => $ride
            ]
        );
    }

    public function deleteAction(Request $request, $citySlug, $rideDate, $photoId = 0)
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

        return $this->redirect($this->generateUrl('caldera_criticalmass_photo_manage',
            [
                'citySlug' => $photo->getRide()->getCity()->getMainSlugString(),
                'rideDate' => $photo->getRide()->getFormattedDate()
            ]
        ));
    }

    public function manageAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $query = $this->getPhotoRepository()->buildQueryPhotosByUserAndRide($this->getUser(), $ride);

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            32
        );

        return $this->render('CalderaCriticalmassSiteBundle:PhotoManagement:manage.html.twig',
            [
                'ride' => $ride,
                'pagination' => $pagination
            ]
        );
    }

    public function toggleAction(Request $request, $citySlug, $rideDate, $photoId)
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

        return $this->redirectToRoute('caldera_criticalmass_photo_manage',
            [
                'citySlug' => $photo->getRide()->getCity()->getMainSlugString(),
                'rideDate' => $photo->getRide()->getFormattedDate()
            ]);
    }

    public function featuredPhotoAction(Request $request, $citySlug, $rideDate, $photoId)
    {
        /**
         * @var Photo $photo
         */
        $photo = $this->getPhotoByIdCitySlugRideDate($citySlug, $rideDate, $photoId);

        $photo->getRide()->setFeaturedPhoto($photo);

        $em = $this->getDoctrine()->getManager();
        $em->persist($photo->getRide());
        $em->flush();


        return $this->redirectToRoute('caldera_criticalmass_photo_manage',
            [
                'citySlug' => $photo->getRide()->getCity()->getMainSlugString(),
                'rideDate' => $photo->getRide()->getFormattedDate()
            ]);
    }


    protected function getPhotoByIdCitySlugRideDate($citySlug, $rideDate, $photoId)
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

    public function placeSingleAction(Request $request, $citySlug, $rideDate, $photoId)
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

    protected function placeSingleGetAction(Request $request, Photo $photo, Form $form)
    {
        $previousPhoto = $this->getPhotoRepository()->getPreviousPhoto($photo);
        $nextPhoto = $this->getPhotoRepository()->getNextPhoto($photo);

        $track = $this->getTrackRepository()->findByUserAndRide($photo->getRide(), $this->getUser());

        return $this->render('CalderaCriticalmassSiteBundle:PhotoManagement:place.html.twig',
            [
                'photo' => $photo,
                'previousPhoto' => $previousPhoto,
                'nextPhoto' => $nextPhoto,
                'track' => $track,
                'form' => $form->createView()
            ]
        );
    }

    protected function placeSinglePostAction(Request $request, Photo $photo, Form $form)
    {
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($form->getData());
            $em->flush();
        }

        return $this->redirectToRoute(
            'caldera_criticalmass_photo_manage',
            [
                'citySlug' => $photo->getRide()->getCity()->getMainSlugString(),
                'rideDate' => $photo->getRide()->getFormattedDate()
            ]
        );
    }

    public function relocateAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $photos = $this->getPhotoRepository()->findPhotosByUserAndRide($this->getUser(), $ride);

        $track = $this->getTrackRepository()->findByUserAndRide($ride, $this->getUser());

        return $this->render('CalderaCriticalmassSiteBundle:PhotoManagement:relocate.html.twig',
            [
                'ride' => $ride,
                'photos' => $photos,
                'track' => $track
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
            'CalderaCriticalmassSiteBundle:PhotoManagement:citygallery.html.twig',
            [
                'photos' => $photos,
                'cities' => $cityList
            ]
        );
    }

    public function rotateAction(Request $request, $citySlug, $rideDate, $photoId)
    {
        /**
         * @var Photo $photo
         */
        $photo = $this->getPhotoByIdCitySlugRideDate($citySlug, $rideDate, $photoId);

        $rotate = 90;

        if ($request->query->get('rotate') && $request->query->get('rotate') == 'right') {
            $rotate = -90;
        }

        $path = $this->getParameter('kernel.root_dir').'/../web';
        $filename = $this->get('vich_uploader.templating.helper.uploader_helper')->asset($photo, 'imageFile');

        $image = imagecreatefromjpeg($path.$filename);
        $image = imagerotate($image, $rotate, 0);
        imagejpeg($image, $path.$filename, 100);
        imagedestroy($image);

        $this->recachePhoto($photo);

        return $this->redirect($this->generateUrl('caldera_criticalmass_photo_manage',
            [
                'citySlug' => $photo->getRide()->getCity()->getMainSlugString(),
                'rideDate' => $photo->getRide()->getFormattedDate()
            ]
        ));
    }

    protected function recachePhoto(Photo $photo)
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
}
