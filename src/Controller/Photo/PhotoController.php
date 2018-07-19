<?php declare(strict_types=1);

namespace App\Controller\Photo;

use App\Controller\AbstractController;
use App\Entity\Photo;
use App\Entity\Track;
use App\Criticalmass\SeoPage\SeoPage;
use App\Event\View\ViewEvent;
use PHPExif\Exif;
use PHPExif\Reader\Reader;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Criticalmass\Feature\Annotation\Feature as Feature;

class PhotoController extends AbstractController
{
    /**
     * @ParamConverter("photo", class="App:Photo", options={"id" = "photoId"})
     * @Feature(name="photos")
     */
    public function showAction(
        SeoPage $seoPage,
        EventDispatcherInterface $eventDispatcher,
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

        $eventDispatcher->dispatch(ViewEvent::NAME, new ViewEvent($photo));

        if ($ride && $photo->getUser()) {
            /** @var Track $track */
            $track = $this->getTrackRepository()->findByUserAndRide($ride, $photo->getUser());
        }

        $seoPage->setPreviewPhoto($photo);

        return $this->render('Photo/show.html.twig', [
            'photo' => $photo,
            'nextPhoto' => $this->getPhotoRepository()->getNextPhoto($photo),
            'previousPhoto' => $this->getPhotoRepository()->getPreviousPhoto($photo),
            'city' => $city,
            'ride' => $ride,
            'track' => $track,
            'exifData' => $this->readExifData($photo)->getData(),
        ]);
    }

    public function ajaxphotoviewAction(Request $request, EventDispatcherInterface $eventDispatcher): Response
    {
        $this->errorIfFeatureDisabled('photos');

        $photoId = $request->get('photoId');

        /** @var Photo $photo */
        $photo = $this->getPhotoRepository()->find($photoId);

        if ($photo) {
            $eventDispatcher->dispatch(ViewEvent::NAME, new ViewEvent($photo));
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
