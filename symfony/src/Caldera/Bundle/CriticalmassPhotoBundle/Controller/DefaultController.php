<?php

namespace Caldera\Bundle\CriticalmassPhotoBundle\Controller;

use Caldera\Bundle\CalderaBundle\Entity\Content;
use Caldera\Bundle\CalderaBundle\Entity\Photo;
use Caldera\Bundle\CalderaBundle\Entity\Track;
use Caldera\Bundle\CriticalmassCoreBundle\BaseTrait\ViewStorageTrait;
use Caldera\Bundle\CriticalmassSiteBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DefaultController extends AbstractController
{
    use ViewStorageTrait;

    public function indexAction(Request $request)
    {
        $galleryResult = $this->getPhotoRepository()->findRidesForGallery();

        return $this->render(
            'CalderaCriticalmassPhotoBundle:Default:index.html.twig',
            [
                'galleryResult' => $galleryResult
            ]
        );
    }
    
    public function listAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        $query = $this->getPhotoRepository()->buildQueryPhotosByRide($ride);

        $paginator = $this->get('knp_paginator');

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            64
        );

        $this->getMetadata()->setDescription('Foto-Galerie von der '.$ride->getCity()->getTitle().' am '.$ride->getDateTime()->format('d.m.Y'));
        
        return $this->render(
            'CalderaCriticalmassPhotoBundle:Default:list.html.twig',
            [
                'ride' => $ride,
                'pagination' => $pagination
            ]
        );
    }

    public function showAction(Request $request, $citySlug, $rideDate, $photoId)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        /** @var Photo $photo */
        $photo = $this->getPhotoRepository()->find($photoId);

        $previousPhoto = $this->getPhotoRepository()->getPreviousPhoto($photo);
        $nextPhoto = $this->getPhotoRepository()->getNextPhoto($photo);

        $this->countPhotoView($photo);

        $exifData = $this
            ->get('caldera.criticalmass.image.exifreader')
            ->setPhoto($photo)
            ->execute();

        $this->getMetadata()->setDescription('Foto von der '.$ride->getCity()->getTitle().' am '.$ride->getDateTime()->format('d.m.Y'));

        /** @var Track $track */
        $track = null;

        if ($ride && $photo->getUser()) {
            $track = $this->getTrackRepository()->findByUserAndRide($ride, $photo->getUser());
        }

        return $this->render('CalderaCriticalmassPhotoBundle:Default:show.html.twig',
            [
                'photo' => $photo,
                'nextPhoto' => $nextPhoto,
                'previousPhoto' => $previousPhoto,
                'track' => $track,
                'exif' => $exifData
            ]
        );
    }

    public function contentAction(Request $request, $contentSlug)
    {
        /** @var Content $content */
        $content = $this->getContentRepository()->findBySlug($contentSlug);

        if (!$content) {
            throw new NotFoundHttpException('Schade, unter dem Stichwort '.$contentSlug.' wurde kein Inhalt hinterlegt.');
        }

        return $this->render(
            'CalderaCriticalmassPhotoBundle:Default:content.html.twig',
            [
                'content' => $content
            ]
        );
    }
}
