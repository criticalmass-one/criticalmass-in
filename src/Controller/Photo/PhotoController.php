<?php declare(strict_types=1);

namespace App\Controller\Photo;

use App\Controller\AbstractController;
use App\Criticalmass\Image\ExifWrapper\ExifWrapperInterface;
use App\Criticalmass\SeoPage\SeoPageInterface;
use App\Entity\Photo;
use App\Entity\Track;
use App\Event\View\ViewEvent;
use App\Repository\PhotoRepository;
use App\Repository\TrackRepository;
use Flagception\Bundle\FlagceptionBundle\Attribute\Feature;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[Feature('photos')]
class PhotoController extends AbstractController
{
    public function showAction(
        SeoPageInterface $seoPage,
        EventDispatcherInterface $eventDispatcher,
        TrackRepository $trackRepository,
        ExifWrapperInterface $exifWrapper,
        Photo $photo
    ): Response {
        if (!$photo->isEnabled() || $photo->isDeleted()) {
            throw $this->createAccessDeniedException();
        }

        $city = $photo->getCity();

        $ride = $photo->getRide();

        /** @var Track $track */
        $track = null;

        if ($ride && $ride->getRestrictedPhotoAccess() && !$this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $eventDispatcher->dispatch(new ViewEvent($photo), ViewEvent::NAME);

        if ($ride && $photo->getUser()) {
            /** @var Track $track */
            $track = $trackRepository->findByUserAndRide($ride, $photo->getUser());
        }

        $this->setSeoMetaDetails($seoPage, $photo);

        return $this->render('Photo/show.html.twig', [
            'photo' => $photo,
            'city' => $city,
            'ride' => $ride,
            'track' => $track,
        ]);
    }

    public function ajaxphotoviewAction(
        Request $request,
        PhotoRepository $photoRepository,
        EventDispatcherInterface $eventDispatcher
    ): Response {
        $photoId = $request->get('photoId');

        /** @var Photo $photo */
        $photo = $photoRepository->find($photoId);

        if ($photo) {
            $eventDispatcher->dispatch(new ViewEvent($photo), ViewEvent::NAME);
        }

        return new Response(null);
    }

    protected function setSeoMetaDetails(SeoPageInterface $seoPage, Photo $photo): void
    {
        $seoPage->setPreviewPhoto($photo);

        if ($photo->getLocation()) {
            $title = sprintf('Fotos von der Critical Mass in %s am %s, %s', $photo->getRide()->getCity()->getCity(), $photo->getRide()->getDateTime()->format('d.m.Y'), $photo->getLocation());
            $description = sprintf('Schau dir Fotos von der %s an, aufgenommen am %s', $photo->getRide()->getTitle(), $photo->getLocation());
        } else {
            $title = sprintf('Fotos von der Critical Mass in %s am %s', $photo->getRide()->getCity()->getCity(), $photo->getRide()->getDateTime()->format('d.m.Y'));
            $description = sprintf('Schau dir Fotos von der %s an', $photo->getRide()->getTitle());
        }

        $seoPage
            ->setTitle($title)
            ->setDescription($description);
    }
}
