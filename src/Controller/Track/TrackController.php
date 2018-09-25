<?php declare(strict_types=1);

namespace App\Controller\Track;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Controller\AbstractController;
use App\Entity\Track;
use Symfony\Component\HttpFoundation\Response;

class TrackController extends AbstractController
{
    /**
     * @Security("is_granted('view', track)")
     * @ParamConverter("track", class="App:Track", options={"id" = "trackId"})
     */
    public function viewAction(Track $track): Response
    {
        return $this->render('Track/view.html.twig', [
            'track' => $track,
            'nextTrack' => $this->getTrackRepository()->getNextTrack($track),
            'previousTrack' => $this->getTrackRepository()->getPreviousTrack($track),
        ]);
    }
}
