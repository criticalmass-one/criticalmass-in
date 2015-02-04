<?php

namespace Caldera\CriticalmassGalleryBundle\Controller;

use Caldera\CriticalmassGalleryBundle\Entity\Photo;
use Caldera\CriticalmassGalleryBundle\Utility\PhotoUploader\PhotoUploader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PhotosController extends Controller
{
    public function indexAction() {
        $criteria = array('enabled' => true);
        $photos = $this->getDoctrine()->getRepository('CriticalmassGalleryBundle:Photo')->findBy($criteria, array('dateTime' => 'DESC'));
        return $this->render('CriticalmassGalleryBundle:Default:list.html.twig', array('photos' => $photos));
    }

    public function listAction(Request $request, $cityId = null, $rideId = null) {
        /* We do not want disabled posts. */
        $criteria = array('enabled' => true);

        /* If a $cityId is provided, add the city to t he criteria. */
        if ($cityId)
        {
            $criteria['city'] = $cityId;
        }

        /* If a $rideId is provided, add the ride to the criteria. */
        if ($rideId)
        {
            $criteria['ride'] = $rideId;
        }

        $photos = $this->getDoctrine()->getRepository('CriticalmassGalleryBundle:Photo')->findBy($criteria, array('dateTime' => 'DESC'));

        return $this->render('CriticalmassGalleryBundle:Default:list.html.twig', array('photos' => $photos));
    }

    public function editAction(Request $request, $photoId=0) {
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

    public function showAction(Request $request, $photoId) {
        $em = $this->getDoctrine()->getManager();
        $photo = $em->find('CriticalmassGalleryBundle:Photo', $photoId);

        return $this->render('CriticalmassGalleryBundle:Default:show.html.twig', array('photo' => $photo));
    }

    public function deleteAction(Request $request, $photoId = 0)
    {
        if ($photoId > 0) {
            $em = $this->getDoctrine()->getManager();
            $photo = $em->find('CriticalmassGalleryBundle:Photo',$photoId);
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
            $photo = $em->find('CriticalmassGalleryBundle:Photo',$photoId);

            $content = "Es wurde das Bild mit der ID " + $photoId + "gemeldet.";

            mail("malte@criticalmass.in", "Bild gemeldet", $content, "malte@criticalmass.in");
        }

        return new RedirectResponse($this->container->get('request')->headers->get('referer'));
    }

    public function changeAction(Request $request, $photoId, $latitude, $longitude) {
        if ($photoId > 0) {
            $em = $this->getDoctrine()->getManager();
            $photo = $em->find('CriticalmassGalleryBundle:Photo',$photoId);

            $photo->setLatitude($latitude);
            $photo->setLongitude($longitude);

            $em->merge($photo);
            $em->flush();
        }

        return new RedirectResponse($this->container->get('request')->headers->get('referer'));
    }

    public function uploadAction(Request $request, $cityId = 0, $rideId = 0) {
        $photo = new Photo();

        error_log($rideId);

        if ($cityId) {
            $city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->find($cityId);
            $photo->setCity($city);
        }
        elseif ($rideId) {
            $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->find($rideId);
            $city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->find($ride->getCity());

            $photo->setCity($city);
            $photo->setRide($ride);
        }

        if (($request->getMethod() == 'POST') &&
            (strtolower($request->files->get('file')->getClientOriginalExtension()) == "jpg"))
        {
            $em = $this->getDoctrine()->getManager();

            $photo->setFile($request->files->get('file'));
            $photo->setUser($this->getUser());

            $em->persist($photo);
            $em->flush();

            $pu = new PhotoUploader();
            $pu->setPhoto($photo);
            $pu->execute();
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
*/
            //}

//            $em->merge($photo);
  //          $em->flush();

            return $this->redirect($this->generateUrl('criticalmass_gallery_photos_index'));
        }

        return $this->render('CriticalmassGalleryBundle:Default:upload.html.twig', array('cityId' => $cityId, 'rideId' => $rideId));
    }
}
