<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Controller\AbstractController;
use App\Entity\Track;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TrackController extends AbstractController
{
    #[IsGranted('view', 'track')]
    public function viewAction(Track $track): Response
    {
        return $this->render('Track/view.html.twig', [
            'track' => $track,
        ]);
    }

    #[IsGranted('approve', 'track')]
    public function approveAction(Track $track, ManagerRegistry $registry): Response
    {
        $track->setReviewed(true);

        $registry->getManager()->flush();

        return $this->redirectToRoute('caldera_criticalmass_track_view', [
            'id' => $track->getId(),
        ]);
    }
}
