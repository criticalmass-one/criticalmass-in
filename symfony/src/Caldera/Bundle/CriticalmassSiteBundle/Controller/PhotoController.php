<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Image\PhotoResizer\PhotoResizer;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Photo;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Ride;
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

    public function listAction(Request $request, $cityId = null, $rideId = null)
    {
        /* We do not want disabled posts. */
        $criteria = array('enabled' => true);

        /* If a $cityId is provided, add the city to t he criteria. */
        if ($cityId) {
            $criteria['city'] = $cityId;
        }

        /* If a $rideId is provided, add the ride to the criteria. */
        if ($rideId) {
            $criteria['ride'] = $rideId;
        }

        $photos = $this->getDoctrine()->getRepository('CalderaCriticalmassGalleryBundle:Photo')->findBy($criteria, array('dateTime' => 'DESC'));

        return $this->render('CalderaCriticalmassGalleryBundle:Default:list.html.twig', array('photos' => $photos));
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

    public function showAction(Request $request, $photoId)
    {
        $photo = $this->getPhotoRepository()->find($photoId);

        return $this->render('CalderaCriticalmassSiteBundle:Photo:show.html.twig',
            [
                'photo' => $photo,
                'nextPhoto' => null,
                'previousPhoto' => null
            ]
        );
    }

    public function deleteAction(Request $request, $photoId = 0)
    {
        if ($photoId > 0) {
            $em = $this->getDoctrine()->getManager();
            $photo = $em->find('CriticalmassGalleryBundle:Photo', $photoId);
            $comments = $this->getDoctrine()->getRepository('CalderaCriticalmassTimelineBundle:Post')->findBy(array('photo' => $photo));
            foreach ($comments as $comment) {
                $em->remove($comment);
            }
            $em->remove($photo);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('criticalmass_gallery_photos_list'));
    }

    public function reportAction(Request $request, $photoId = 0)
    {
        if ($photoId > 0) {
            $em = $this->getDoctrine()->getManager();
            $photo = $em->find('CriticalmassGalleryBundle:Photo', $photoId);

            $content = "Es wurde das Bild mit der ID " + $photoId + "gemeldet.";

            mail("malte@criticalmass.in", "Bild gemeldet", $content, "malte@criticalmass.in");
        }

        return new RedirectResponse($this->container->get('request')->headers->get('referer'));
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


        /*
        $photo->handleUpload();

        $em = $this->getDoctrine()->getManager();

        $track = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Track')->findBy(array('user' => $photo->getUser(), 'ride' => $photo->getRide()));

        $utility = new PhotoUtility();

        $utility->approximateCoordinates($photo, $track);

        if (!($photo->getLatitude() && $photo->getLongitude())) {
                            $track = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Track')->findBy(array('user' => $photo->getUser(), 'ride' => $photo->getRide()));

                            $utility = new PhotoUtility();

                            $utility->approximateCoordinates($photo, $track);

                       }

        $em->merge($photo);
        $em->flush();*/

        return new Response('foo');
    }

    public function userlistAction(Request $request)
    {
        $rides = $this->getRideRepository()->findRidesWithPhotosByUser($this->getUser());

        /*return $this->render('CalderaCriticalmassSiteBundle:Photo:upload.html.twig', [
            'ride' => $ride
        ]);*/

        foreach ($rides as $ride)
        {
            echo "FOOO: ".$ride->getId()."<br />";

        }
        return new Response('');
    }

    public function manageAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);
        
        $photos = $this->getPhotoRepository()->findPhotosByRide($ride);
        
        return $this->render('CalderaCriticalmassSiteBundle:Photo:manage.html.twig', [
            'photos' => $photos
        ]);
    }
}
