<?php

namespace Caldera\CriticalmassGalleryBundle\Controller;

use Caldera\CriticalmassCoreBundle\Utility\GpxReader\GpxReader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Caldera\CriticalmassGalleryBundle\Entity\Photos;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Validator\Constraints\DateTime;

class PhotosController extends Controller
{
    public function indexAction() {
        $photos = $this->getDoctrine()->getRepository('CriticalmassGalleryBundle:Photos')->findBy(array('enabled' => true));
        return $this->render('CriticalmassGalleryBundle:Default:index.html.twig', array('photos' => $photos));
    }

    public function listAction() {
        $photos = $this->getDoctrine()->getRepository('CriticalmassGalleryBundle:Photos')->findBy(array('enabled' => true));
        return $this->render('CriticalmassGalleryBundle:Default:list.html.twig', array('photos' => $photos));
    }

    public function addAction(Request $request, $cityId = 0, $rideId = 0) {
        $photo = new Photos();
        if ($cityId) {

            $form = $this->createFormBuilder($photo)
                ->setAction($this->generateUrl('criticalmass_gallery_photos_add_city', array('cityId' => $cityId)))
                ->add('file')
                ->getForm();

            $city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->find($cityId);
            $photo->setCity($city);
        }
        elseif ($rideId)
        {
            $form = $this->createFormBuilder($photo)
                ->setAction($this->generateUrl('criticalmass_gallery_photos_add_ride', array('rideId' => $rideId)))
                ->add('file')
                ->getForm();

            $ride = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Ride')->find($rideId);
            $city = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:City')->find($ride->getCity());
            $photo->setCity($city);
            $photo->setRide($ride);
        }
        else
        {
            $form = $this->createFormBuilder($photo)
                ->setAction($this->generateUrl('criticalmass_gallery_photos_add'))
                ->add('file')
                ->getForm();
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $photo->setUser($this->getUser());
            $photo->setDescription("");
            $photo->setFilePath("");

            $em->persist($photo);
            $em->flush();

            $photo->handleUpload();

            $em = $this->getDoctrine()->getManager();

            $track = $this->getDoctrine()->getRepository('CalderaCriticalmassCoreBundle:Track')->findBy(array('user' => $photo->getUser(), 'ride' => $photo->getRide()));

            if (count($track)) {
                $gpxReader = new GpxReader();
                $gpxReader->loadString($track[0]->getGpx());
                $finished = 0;
                $photo->setDateTime(new \DateTime("2014-08-29 17:19:14"));
                error_log($photo->getDateTime()->format('Y-m-d H:i:s'));
                $tmpDatetimePrev = new \DateTime(str_replace("T", " ", str_replace("Z", "", $gpxReader->getTimestampOfPoint(0))));
                $tmpDatetimeSucc = new \DateTime(str_replace("T", " ", str_replace("Z", "", $gpxReader->getTimestampOfPoint(1))));
                error_log($tmpDatetimePrev->format('Y-m-d H:i:s'));
                error_log($tmpDatetimeSucc->format('Y-m-d H:i:s'));
                error_log($tmpDatetimePrev <= $photo->getDateTime());
                error_log($photo->getDateTime() <= $tmpDatetimeSucc);
                error_log($photo->getDateTime()->format('Y-m-d H:i:s'));
                for ($i = 0; $i < $gpxReader->countPoints() - 1 || $finished; $i++) {
                    $tmpDatetimePrev = new \DateTime(str_replace("T", " ", str_replace("Z", "", $gpxReader->getTimestampOfPoint($i))));
                    $tmpDatetimeSucc = new \DateTime(str_replace("T", " ", str_replace("Z", "", $gpxReader->getTimestampOfPoint($i+1))));
                    if (($gpxReader->getTimestampOfPoint($i) <= $photo->getDateTime()) &&
                        ($gpxReader->getTimestampOfPoint($i+1) >= $photo->getDateTime())) {
                        $timeDiffPrev = $tmpDatetimePrev->diff($photo->getDateTime());
                        $timeDiffPrevSec = $timeDiffPrev->format('%s') + 60*$timeDiffPrev->format("%i") + 3600*$timeDiffPrev->format("%H");
                        $timeDiffSucc = $photo->getDateTime()->diff($tmpDatetimeSucc);
                        $timeDiffSuccSec = $timeDiffSucc->format('%s') + 60*$timeDiffSucc->format("%i") + 3600*$timeDiffSucc->format("%H");
                        $timespan = $timeDiffPrevSec + $timeDiffSuccSec;
                        error_log($gpxReader->getLatitudeOfPoint($i));

                        $finished = 1;
                    }
                }
            }

            $em->merge($photo);
            $em->flush();

            return $this->redirect($this->generateUrl('criticalmass_gallery_photos_list'));
        }

        return $this->render('CriticalmassGalleryBundle:Default:add.html.twig', array('form' => $form->createView()));
    }

    public function editAction(Request $request, $photoId=0) {
        if ($photoId > 0) {
            $em = $this->getDoctrine()->getManager();
            $photo = $em->find('CriticalmassGalleryBundle:Photos', $photoId);
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
        $photo = $em->find('CriticalmassGalleryBundle:Photos', $photoId);

        return $this->render('CriticalmassGalleryBundle:Default:show.html.twig', array('photo' => $photo));
    }

    public function deleteAction(Request $request, $photoId = 0)
    {
        if ($photoId > 0) {
            $em = $this->getDoctrine()->getManager();
            $photo = $em->find('CriticalmassGalleryBundle:Photos',$photoId);
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
            $post = $em->find('CriticalmassGalleryBundle:Photos',$photoId);

            $content = "Es wurde das Bild mit der ID " + $photoId + "gemeldet.";

            mail("malte@criticalmass.in", "Bild gemeldet", $content, "malte@criticalmass.in");
        }

        return new RedirectResponse($this->container->get('request')->headers->get('referer'));
    }
}
