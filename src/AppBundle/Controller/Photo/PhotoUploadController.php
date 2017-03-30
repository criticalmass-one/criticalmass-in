<?php

namespace AppBundle\Controller\Photo;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Photo;
use AppBundle\Entity\Ride;
use AppBundle\Image\ExifReader\DateTimeExifReader;
use AppBundle\Image\PhotoGps\PhotoGps;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PhotoUploadController extends AbstractController
{
    public function uploadAction(Request $request, $citySlug, $rideDate): Response
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        if ($request->getMethod() == 'POST') {
            return $this->uploadPostAction($request, $ride);
        } else {
            return $this->uploadGetAction($request, $ride);
        }
    }

    protected function uploadGetAction(Request $request, Ride $ride): Response
    {
        return $this->render(
            'AppBundle:PhotoUpload:upload.html.twig',
            [
                'ride' => $ride,
            ]
        );
    }

    protected function uploadPostAction(Request $request, Ride $ride): Response
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

        $track = null;

        if ($ride) {
            $track = $this->getTrackRepository()->findByUserAndRide($ride, $this->getUser());
        }

        if ($ride && $track) {
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
}
