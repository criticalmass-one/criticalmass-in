<?php declare(strict_types=1);

namespace AppBundle\Controller\Track;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Controller\AbstractController;
use AppBundle\Entity\Track;
use Symfony\Component\HttpFoundation\Response;

class TrackController extends AbstractController
{
    /**
     * @Security("is_granted('view', track)")
     * @ParamConverter("track", class="AppBundle:Track", options={"id" = "trackId"})
     */
    public function viewAction(Track $track): Response
    {
        return $this->render('AppBundle:Track:view.html.twig', [
            'track' => $track,
            'nextTrack' => $this->getTrackRepository()->getNextTrack($track),
            'previousTrack' => $this->getTrackRepository()->getPreviousTrack($track),
        ]);
    }
}
