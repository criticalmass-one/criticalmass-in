<?php

namespace AppBundle\Controller\Photo;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Photo;
use AppBundle\Entity\Ride;
use AppBundle\Image\ExifReader\DateTimeExifReader;
use AppBundle\Image\PhotoGps\PhotoGps;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class PhotoUploadController extends AbstractController
{
    public function uploadAction(Request $request, UserInterface $user, string $citySlug, string $rideDate): Response
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        if ($request->getMethod() == 'POST') {
            return $this->uploadPostAction($request, $user, $ride);
        } else {
            return $this->uploadGetAction($request, $user, $ride);
        }
    }

    protected function uploadGetAction(Request $request, UserInterface $user, Ride $ride): Response
    {
        return $this->render(
            'AppBundle:PhotoUpload:upload.html.twig',
            [
                'ride' => $ride,
            ]
        );
    }

    protected function uploadPostAction(Request $request, UserInterface $user, Ride $ride): Response
    {
        $em = $this->getDoctrine()->getManager();

        $photo = new Photo();

        $photo->setImageFile($request->files->get('file'));
        $photo->setUser($this->getUser());

        $photo->setRide($ride);
        $photo->setCity($ride->getCity());

        $em->persist($photo);
        $em->flush();

        $this->findDateTime($photo);
        $this->findCoords($ride, $photo, $user);

        $em->flush();

        return new Response('');
    }

    protected function findDateTime(Photo $photo): bool
    {
        try {
            /**
             * @var DateTimeExifReader $dter
             */
            $dter = $this->get('caldera.criticalmass.image.exifreader.datetime');

            $dateTime = $dter
                ->setPhoto($photo)
                ->execute()
                ->getDateTime();

            $photo->setDateTime($dateTime);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    protected function findCoords(Ride $ride, Photo $photo, UserInterface $user): bool
    {
        $track = null;

        if ($ride) {
            $track = $this->getTrackRepository()->findByUserAndRide($ride, $user);
        }

        if ($ride && $track) {
            try {
                /**
                 * @var PhotoGps $pgps
                 */
                $pgps = $this->get('caldera.criticalmass.image.photogps');

                $pgps
                    ->setPhoto($photo)
                    ->setTrack($track)
                    ->execute()
                ;
            } catch (\Exception $e) {
                return false;
            }

            return true;
        }

        return false;
    }
}
