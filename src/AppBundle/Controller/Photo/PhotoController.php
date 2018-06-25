<?php

namespace AppBundle\Controller\Photo;

use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Photo;
use AppBundle\Entity\Track;
use AppBundle\Criticalmass\SeoPage\SeoPage;
use AppBundle\Criticalmass\ViewStorage\ViewStorageCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PhotoController extends AbstractController
{
    /**
     * @ParamConverter("photo", class="AppBundle:Photo", options={"id" = "photoId"})
     */
    public function showAction(Request $request, SeoPage $seoPage, ViewStorageCache $viewStorageCache, Photo $photo): Response
    {
        $this->errorIfFeatureDisabled('photos');

        $city = $photo->getCity();

        $ride = $photo->getRide();

        /** @var Track $track */
        $track = null;

        if ($ride && $ride->getRestrictedPhotoAccess() && !$this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $previousPhoto = $this->getPhotoRepository()->getPreviousPhoto($photo);
        $nextPhoto = $this->getPhotoRepository()->getNextPhoto($photo);

        $viewStorageCache->countView($photo);

        if ($ride && $photo->getUser()) {
            /** @var Track $track */
            $track = $this->getTrackRepository()->findByUserAndRide($ride, $photo->getUser());
        }

        $seoPage->setPreviewPhoto($photo);

        return $this->render('AppBundle:Photo:show.html.twig', [
            'photo' => $photo,
            'nextPhoto' => $nextPhoto,
            'previousPhoto' => $previousPhoto,
            'city' => $city,
            'ride' => $ride,
            'track' => $track,
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
}
