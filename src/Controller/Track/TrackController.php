<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Controller\AbstractController;
use App\Entity\Track;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TrackController extends AbstractController
{
    /**
     * @ParamConverter("track", class="App:Track", options={"id" = "trackId"})
     */
    #[IsGranted('view', 'track')]
    public function viewAction(Track $track): Response
    {
        return $this->render('Track/view.html.twig', [
            'track' => $track,
        ]);
    }

    /**
     * @ParamConverter("track", class="App:Track", options={"id" = "trackId"})
     */
    #[IsGranted('approve', 'track')]
    public function approveAction(Track $track, ManagerRegistry $registry): Response
    {
        $track->setReviewed(true);

        $registry->getManager()->flush();

        return $this->redirectToRoute('caldera_criticalmass_track_view', [
            'trackId' => $track->getId(),
        ]);
    }
}
