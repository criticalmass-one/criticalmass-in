<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Event\Track\TrackDeletedEvent;
use App\Event\Track\TrackHiddenEvent;
use App\Event\Track\TrackShownEvent;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Controller\AbstractController;
use App\Entity\Track;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

class TrackDownloadController extends AbstractController
{
    /**
     * @Security("is_granted('edit', track)")
     * @ParamConverter("track", class="App:Track", options={"id" = "trackId"})
     */
    public function downloadAction(Track $track): Response
    {
        $trackContent = file_get_contents($this->getTrackFilename($track));

        $response = new Response();

        $response->headers->add([
            'Content-disposition' => 'attachment; filename=track.gpx',
            'Content-type',
            'text/plain',
        ]);

        $response->setContent($trackContent);

        return $response;
    }

    protected function getTrackFilename(Track $track): string
    {
        $rootDirectory = $this->getParameter('kernel.root_dir');
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        $filename = $helper->asset($track, 'trackFile');

        return $rootDirectory . '/../web' . $filename;
    }
}
