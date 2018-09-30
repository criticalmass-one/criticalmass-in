<?php declare(strict_types=1);

namespace App\Controller\Photo;

use App\Controller\AbstractController;
use App\Criticalmass\SeoPage\SeoPageInterface;
use App\Entity\Photo;
use App\Entity\Track;
use App\Event\View\ViewEvent;
use PHPExif\Exif;
use PHPExif\Reader\Reader;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Criticalmass\Feature\Annotation\Feature as Feature;

/**
 * @Feature(name="photos")
 */
class PhotoController extends AbstractController
{
    /**
     * @ParamConverter("photo", class="App:Photo", options={"id" = "photoId"})
     */
    public function showAction(
        SeoPageInterface $seoPage,
        EventDispatcherInterface $eventDispatcher,
        Photo $photo
    ): Response {
        $city = $photo->getCity();

        $ride = $photo->getRide();

        /** @var Track $track */
        $track = null;

        if ($ride && $ride->getRestrictedPhotoAccess() && !$this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $eventDispatcher->dispatch(ViewEvent::NAME, new ViewEvent($photo));

        if ($ride && $photo->getUser()) {
            /** @var Track $track */
            $track = $this->getTrackRepository()->findByUserAndRide($ride, $photo->getUser());
        }

        $this->setSeoMetaDetails($seoPage, $photo);

        $exifData = $this->readExifData($photo);

        return $this->render('Photo/show.html.twig', [
            'photo' => $photo,
            'nextPhoto' => $this->getPhotoRepository()->getNextPhoto($photo),
            'previousPhoto' => $this->getPhotoRepository()->getPreviousPhoto($photo),
            'city' => $city,
            'ride' => $ride,
            'track' => $track,
            'exifData' => $exifData ? $exifData->getData() : null,
        ]);
    }

    public function ajaxphotoviewAction(Request $request, EventDispatcherInterface $eventDispatcher): Response
    {
        $photoId = $request->get('photoId');

        /** @var Photo $photo */
        $photo = $this->getPhotoRepository()->find($photoId);

        if ($photo) {
            $eventDispatcher->dispatch(ViewEvent::NAME, new ViewEvent($photo));
        }

        return new Response(null);
    }

    protected function readExifData(Photo $photo): ?Exif
    {
        $filename = sprintf('%s/%s', $this->getParameter('upload_destination.photo'), $photo->getImageName());

        $reader = Reader::factory(Reader::TYPE_NATIVE);

        if ($exif = $reader->read($filename)) {
            return $exif;
        }

        return null;
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
