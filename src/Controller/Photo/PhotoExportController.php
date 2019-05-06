<?php declare(strict_types=1);

namespace App\Controller\Photo;

use App\Controller\AbstractController;
use App\Entity\Photo;
use Symfony\Component\HttpFoundation\Response;
use Flagception\Bundle\FlagceptionBundle\Annotations\Feature;

/**
 * @Feature("photos")
 */
class PhotoExportController extends AbstractController
{
    public function listAction(): Response
    {
        $photoBasePath = $this->getParameter('upload_destination.photo');

        $photoList = $this->getPhotoRepository()->findAll();

        $tsvList = [];

        /** @var Photo $photo */
        foreach ($photoList as $photo) {
            $filename = sprintf('%s/%s', $photoBasePath, $photo->getImageName());

            $size = @filesize($filename);
            $hash = @sha1_file($filename);

            $webPath = sprintf('http://criticalmass.cm/photos/%s', $photo->getImageName());

            $tsvList[] = sprintf('%s\t%s\t%s', $webPath, $size, $hash);
        }

        return new Response(implode('\n', $tsvList), 200, ['Content-type: text/tsv']);
    }
}
