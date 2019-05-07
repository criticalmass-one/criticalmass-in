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

        $photoList = $this->getPhotoRepository()->findAll();

        $tsvList = [];

        /** @var Photo $photo */
        foreach ($photoList as $photo) {
            if ($photo->getImageName()) {
                $this->addFile($photo, 'imageName', $tsvList);
            }

            if ($photo->getBackupName()) {
                $this->addFile($photo, 'backupName', $tsvList);
            }
        }

        return new Response(implode("\n", $tsvList), 200, ['Content-type: text/tab-separated-values']);
    }

    protected function addFile(Photo $photo, string $propertyName, array &$tsvList): void
    {
        $getMethodName = sprintf('get%s', ucfirst($propertyName));

        $filename = sprintf('%s/%s', $this->getParameter('upload_destination.photo'), $photo->$getMethodName());

        if (!file_exists($filename)) {
            return;
        }

        $size = filesize($filename);
        $hash = md5_file($filename);

        $webPath = sprintf('http://criticalmass.cm/photos/%s', $photo->getImageName());

        $tsvList[] = sprintf("%s\t%s\t%s", $webPath, $size, base64_encode($hash));
    }
}
