<?php declare(strict_types=1);

namespace App\Controller\Track;

use League\Flysystem\Filesystem;
use App\Controller\AbstractController;
use App\Entity\Track;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class TrackDownloadController extends AbstractController
{
    #[IsGranted('edit', 'track')]
    #[Route('/track/download/{id}', name: 'caldera_criticalmass_track_download', priority: 270)]
    public function downloadAction(Track $track, UploaderHelper $uploaderHelper): Response
    {
        /** @var Filesystem $filesystem */
        $filesystem = $this->get('oneup_flysystem.flysystem_track_track_filesystem');
        $trackContent = $filesystem->read($track->getTrackFilename());

        $response = new Response();
        $response->headers->add([
            'Content-disposition' => sprintf('attachment; filename=%s', $this->generateHumanFriendlyFilename($track)),
            'Content-type' => 'application/gpx+xml',
        ]);

        $response->setContent($trackContent);

        return $response;
    }

    protected function generateHumanFriendlyFilename(Track $track): string
    {
        $filename = sprintf(
            'Critical Mass %s %s',
            $track->getRide()->getCity()->getCity(),
            $track->getRide()->getDateTime()->format('Y-m-d')
        );

        return sprintf('%s.gpx', $filename);
    }

    protected function getTrackFilename(Track $track, UploaderHelper $uploaderHelper): string
    {
        return $uploaderHelper->asset($track, 'trackFile');
    }
}
