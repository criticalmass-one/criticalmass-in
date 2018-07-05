<?php

namespace AppBundle\Controller\Photo;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Photo;
use AppBundle\Entity\Track;
use AppBundle\Criticalmass\SeoPage\SeoPage;
use AppBundle\Criticalmass\ViewStorage\ViewStorageCache;
use PHPExif\Exif;
use PHPExif\Reader\Reader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PhotoController extends AbstractController
{
    /**
     * @ParamConverter("photo", class="AppBundle:Photo", options={"id" = "photoId"})
     */
    public function showAction(
        Request $request,
        SeoPage $seoPage,
        ViewStorageCache $viewStorageCache,
        Photo $photo
    ): Response {
        $this->errorIfFeatureDisabled('photos');

        $city = $photo->getCity();

        $ride = $photo->getRide();

        /** @var Track $track */
        $track = null;

        if ($ride && $ride->getRestrictedPhotoAccess() && !$this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $viewStorageCache->countView($photo);

        if ($ride && $photo->getUser()) {
            /** @var Track $track */
            $track = $this->getTrackRepository()->findByUserAndRide($ride, $photo->getUser());
        }

        $seoPage->setPreviewPhoto($photo);

        return $this->render('AppBundle:Photo:show.html.twig', [
            'photo' => $photo,
            'nextPhoto' => $this->getPhotoRepository()->getNextPhoto($photo),
            'previousPhoto' => $this->getPhotoRepository()->getPreviousPhoto($photo),
            'city' => $city,
            'ride' => $ride,
            'track' => $track,
            'exifData' => $this->readExifData($photo),
        ]);
    }

    public function ajaxphotoviewAction(Request $request, ViewStorageCache $viewStorageCache): Response
    {
        $this->errorIfFeatureDisabled('photos');

        $photoId = $request->get('photoId');

        /**
         * @var Photo $photo
         */
        $photo = $this->getPhotoRepository()->find($photoId);

        if ($photo) {
            $viewStorageCache->countView($photo);
        }

        return new Response(null);
    }

    protected function readExifData(Photo $photo): Exif
    {
        $filename = sprintf('%s/%s', $this->getParameter('upload_destination.photo'), $photo->getImageName());

        $reader = Reader::factory(Reader::TYPE_NATIVE);
        $exif = $reader->read($filename);

        return $exif;
    }
}
