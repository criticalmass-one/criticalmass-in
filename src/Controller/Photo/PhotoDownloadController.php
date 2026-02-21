<?php declare(strict_types=1);

namespace App\Controller\Photo;

use App\Controller\AbstractController;
use App\Entity\Photo;
use Flagception\Bundle\FlagceptionBundle\Attribute\Feature;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

#[Feature('photos')]
class PhotoDownloadController extends AbstractController
{
    #[Route('/photo/{id}/download', name: 'caldera_criticalmass_photo_download', priority: 180)]
    #[IsGranted('ROLE_PHOTO_DOWNLOAD')]
    public function downloadAction(
        UploaderHelper $uploaderHelper,
        Photo $photo,
        string $uploadDestinationPhoto
    ): BinaryFileResponse {
        if (!$photo->isEnabled() || $photo->isDeleted()) {
            throw $this->createAccessDeniedException();
        }

        $ride = $photo->getRide();

        if ($ride && $ride->getRestrictedPhotoAccess() && !$this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $filename = sprintf('%s%s', $uploadDestinationPhoto, $uploaderHelper->asset($photo));

        $response = new BinaryFileResponse($filename);

        $downloadFilename = sprintf(
            'criticalmass_%s_%s.jpg',
            $photo->getCity()->getMainSlugString(),
            $photo->getRide()->getDateTime()->format('Y-m-d')
        );

        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $downloadFilename
        );

        return $response;
    }
}
