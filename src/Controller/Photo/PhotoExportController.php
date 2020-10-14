<?php declare(strict_types=1);

namespace App\Controller\Photo;

use App\Controller\AbstractController;
use App\Entity\Photo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Flagception\Bundle\FlagceptionBundle\Annotations\Feature;

/**
 * @Feature("photos")
 */
class PhotoExportController extends AbstractController
{
    public function listAction(Request $request): Response
    {
        if ($request->get('access_key') !== $this->getParameter('photo_tsv.access_key')) {
            throw $this->createAccessDeniedException();
        }

        $limit = $request->query->getInt('limit');
        $offset = $request->query->getInt('offset');

        $photoList = $this->getPhotoRepository()->findPhotosForExport($limit, $offset);

        $tsvList = [
            'TsvHttpData-1.0',
        ];

        /** @var Photo $photo */
        foreach ($photoList as $photo) {
            if ($photo->getImageName()) {
                $this->addFile($request, $photo, 'image', $tsvList);
            }

            if ($photo->getBackupName()) {
                $this->addFile($request, $photo, 'backup', $tsvList);
            }
        }

        return new Response(implode("\n", $tsvList), 200, ['Content-type: text/tab-separated-values']);
    }

    protected function addFile(Request $request, Photo $photo, string $propertyPrefix, array &$tsvList): void
    {
        $webPrefix = $request->getSchemeAndHttpHost();

        $imageNameGetMethodName = sprintf('get%sName', $propertyPrefix);
        $imageSizeGetMethodName = sprintf('get%sSize', $propertyPrefix);
        $imageHashGetMethodName = sprintf('get%sGoogleCloudHash', $propertyPrefix);

        $tsvList[] = sprintf("%s\t%s\t%s", sprintf('%s/photos/%s', $webPrefix, $photo->$imageNameGetMethodName()), $photo->$imageSizeGetMethodName(), $photo->$imageHashGetMethodName());
    }
}
