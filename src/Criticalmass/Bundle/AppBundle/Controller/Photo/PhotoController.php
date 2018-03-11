<?php

namespace Criticalmass\Bundle\AppBundle\Controller\Photo;

use Criticalmass\Bundle\AppBundle\Controller\AbstractController;
use Criticalmass\Bundle\AppBundle\Entity\Photo;
use Criticalmass\Bundle\AppBundle\Entity\Track;
use Criticalmass\Component\SeoPage\SeoPage;
use Criticalmass\Component\ViewStorage\ViewStorageCache;
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
