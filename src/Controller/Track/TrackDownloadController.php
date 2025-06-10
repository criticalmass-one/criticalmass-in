<?php declare(strict_types=1);

namespace App\Controller\Track;

use League\Flysystem\Filesystem;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Controller\AbstractController;
use App\Entity\Track;
use Symfony\Component\HttpFoundation\Response;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class TrackDownloadController extends AbstractController
{
    /**
     * @Security("is_granted('edit', track)")
     */
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
        $filename = sprintf('Critical Mass %s %s', $track->getRide()->getCity()->getCity(), $track->getRide()->getDateTime()->format('Y-m-d'));

        return sprintf('%s.gpx', $filename);
    }

    protected function getTrackFilename(Track $track, UploaderHelper $uploaderHelper): string
    {
        return $uploaderHelper->asset($track, 'trackFile');
    }
}
