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

class PhotoController extends AbstractController
{
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
            'CalderaCriticalmassSiteBundle:Photo:ridelist.html.twig',
            [
                'ride' => $ride,
                'pagination' => $pagination
            ]
        );
    }

    public function editAction(Request $request, $photoId = 0)
    {
        if ($photoId > 0) {
            $em = $this->getDoctrine()->getManager();
            $photo = $em->find('CriticalmassGalleryBundle:Photo', $photoId);
            $form = $this->createFormBuilder($photo)
                ->setAction($this->generateUrl('criticalmass_gallery_photos_edit', array('photoId' => $photoId)))
                ->add('description')
                ->getForm();

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->merge($photo);
                $em->flush();

                return $this->redirect($this->generateUrl('criticalmass_gallery_photos_list'));
            }

            return $this->render('CriticalmassGalleryBundle:Default:edit.html.twig', array('form' => $form->createView()));
        }
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

        return $this->render('CalderaCriticalmassSiteBundle:Photo:show.html.twig',
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

    public function uploadAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        if ($request->getMethod() == 'POST') {
            return $this->uploadPostAction($request, $ride);
        } else {
            return $this->render('CalderaCriticalmassSiteBundle:Photo:upload.html.twig', [
                'ride' => $ride
            ]);
        }
    }

    protected function uploadPostAction(Request $request, Ride $ride)
    {
        $em = $this->getDoctrine()->getManager();

        $photo = new Photo();

        $photo->setImageFile($request->files->get('file'));
        $photo->setUser($this->getUser());
        $photo->setRide($ride);
        $photo->setCity($ride->getCity());

        $em->persist($photo);
        $em->flush();

        /**
         * @var DateTimeExifReader $dter
         */
        $dter = $this->get('caldera.criticalmass.image.exifreader.datetime');

        $dateTime = $dter
            ->setPhoto($photo)
            ->execute()
            ->getDateTime();

        $photo->setDateTime($dateTime);

        $em->persist($photo);
        $em->flush();

        $track = $this->getTrackRepository()->findByUserAndRide($ride, $this->getUser());

        if ($track) {
            /**
             * @var PhotoGps $pgps
             */
            $pgps = $this->get('caldera.criticalmass.image.photogps');

            $pgps
                ->setPhoto($photo)
                ->setTrack($track)
                ->execute();

            $em->merge($photo);
            $em->flush();
        }

        return new Response('foo');
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

        return $this->render('CalderaCriticalmassSiteBundle:Photo:manage.html.twig',
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

    protected function getPhotoByIdCitySlugRideDate($citySlug, $rideDate, $photoId)
    {
        /**
         * @var Photo $photo
         */
        $photo = $this->getPhotoRepository()->find($photoId);

        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);
        $photo = $this->getPhotoRepository()->find($photoId);

        if ($ride and
            $photo and
            $photo->getUser()->equals($this->getUser()) and
            $photo->getRide()->equals($ride)
        ) {
            return $photo;
        }

        return null;
    }

    protected function countView(Photo $photo)
    {
        $memcache = $this->get('memcache.criticalmass');

        $additionalPhotoViews = $memcache->get('gallery_photo'.$photo->getId().'_additionalviews');

        if (!$additionalPhotoViews) {
            $additionalPhotoViews = 1;
        } else {
            ++$additionalPhotoViews;
        }

        $viewDateTime = new \DateTime();

        $photoViewArray =
            [
                'photoId' => $photo->getId(),
                'userId' => ($this->getUser() ? $this->getUser()->getId() : null),
                'dateTime' => $viewDateTime->format('Y-m-d H:i:s')
            ]
        ;

        $memcache->set('gallery_photo'.$photo->getId().'_additionalviews', $additionalPhotoViews);
        $memcache->set('gallery_photo'.$photo->getId().'_view'.$additionalPhotoViews, $photoViewArray);

/*
        $photo->incViews();

        $photoView = new PhotoView();
        $photoView->setUser($this->getUser());
        $photoView->setPhoto($photo);

        $em = $this->getDoctrine()->getManager();
        $em->persist($photo);
        $em->persist($photoView);
        $em->flush();*/
    }

    public function ajaxphotoviewAction(Request $request)
    {
        $photoId = $request->get('photoId');

        $photo = $this->getPhotoRepository()->find($photoId);

        if ($photo) {
            $this->countView($photo);
        }

        return new Response(null);
    }

    public function placeSingleAction(Request $request, $citySlug, $rideDate, $photoId)
    {
        /**
         * @var Photo $photo
         */
        $photo = $this->getPhotoByIdCitySlugRideDate($citySlug, $rideDate, $photoId);

        if ($photo) {
            $form = $this->createForm(
                new PhotoCoordType(),
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

        return $this->render('CalderaCriticalmassSiteBundle:Photo:place.html.twig',
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

        return $this->render('CalderaCriticalmassSiteBundle:Photo:relocate.html.twig',
            [
                'photos' => $photos,
                'track' => $track
            ]
        );
    }
}
