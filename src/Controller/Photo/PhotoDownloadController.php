<?php declare(strict_types=1);

namespace App\Controller\Photo;

use App\Controller\AbstractController;
use App\Criticalmass\Image\ExifWrapper\ExifWrapperInterface;
use App\Criticalmass\SeoPage\SeoPageInterface;
use App\Entity\Photo;
use App\Entity\Track;
use App\Event\View\ViewEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Flagception\Bundle\FlagceptionBundle\Annotations\Feature;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * @Feature("photos")
 */
class PhotoDownloadController extends AbstractController
{
    /**
     * @ParamConverter("photo", class="App:Photo", options={"id" = "photoId"})
     */
    public function downloadAction(
        UploaderHelper $uploaderHelper,
        EventDispatcherInterface $eventDispatcher,
        Photo $photo,
        string $uploadDestinationPhoto
    ): Response {
        $city = $photo->getCity();

        $ride = $photo->getRide();

        if ($ride && $ride->getRestrictedPhotoAccess() && !$this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $eventDispatcher->dispatch(ViewEvent::NAME, new ViewEvent($photo));

        $filename = sprintf('%s%s', $uploadDestinationPhoto, $uploaderHelper->asset($photo));

        return new BinaryFileResponse($filename);
    }
}
