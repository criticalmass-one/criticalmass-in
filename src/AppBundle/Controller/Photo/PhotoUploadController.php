<?php

namespace AppBundle\Controller\Photo;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Photo;
use AppBundle\Entity\Ride;
use AppBundle\Criticalmass\Image\PhotoGps\PhotoGps;
use PHPExif\Reader\Reader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class PhotoUploadController extends AbstractController
{
    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("ride", class="AppBundle:Ride")
     */
    public function uploadAction(Request $request, UserInterface $user, PhotoGps $photoGps, Ride $ride): Response
    {
        $this->errorIfFeatureDisabled('photos');

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->uploadPostAction($request, $user, $photoGps, $ride);
        } else {
            return $this->uploadGetAction($request, $user, $photoGps, $ride);
        }
    }

    protected function uploadGetAction(Request $request, UserInterface $user, PhotoGps $photoGps, Ride $ride): Response
    {
        return $this->render('AppBundle:PhotoUpload:upload.html.twig', [
            'ride' => $ride,
        ]);
    }

    protected function uploadPostAction(Request $request, UserInterface $user, PhotoGps $photoGps, Ride $ride): Response
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
        $this->findCoords($photoGps, $ride, $photo, $user);

        $em->flush();

        return new Response('');
    }

    protected function findDateTime(Photo $photo): bool
    {
        try {
            $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
            $path = $this->getParameter('kernel.root_dir') . '/../web/' . $helper->asset($photo, 'imageFile');

            $reader = Reader::factory(Reader::TYPE_NATIVE);

            $exif = $reader->getExifFromFile($path);

            if ($dateTime = $exif->getCreationDate()) {
                $photo->setDateTime($dateTime);
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    protected function findCoords(PhotoGps $photoGps, Ride $ride, Photo $photo, UserInterface $user): bool
    {
        $track = null;

        if ($ride) {
            $track = $this->getTrackRepository()->findByUserAndRide($ride, $user);
        }

        if ($ride && $track) {
            try {
                $photoGps
                    ->setPhoto($photo)
                    ->setTrack($track)
                    ->execute();
            } catch (\Exception $e) {
                return false;
            }

            return true;
        }

        return false;
    }
}
