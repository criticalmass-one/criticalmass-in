<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Controller\AbstractController;
use App\Entity\Track;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TrackController extends AbstractController
{
    #[IsGranted('view', 'track')]
    #[Route(
        '/track/view/{id}',
        requirements: ['id' => '\d+'],
        name: 'caldera_criticalmass_track_view',
        priority: 150
    )]
    public function viewAction(Track $track): Response
    {
        return $this->render('Track/view.html.twig', [
            'track' => $track,
        ]);
    }

    #[IsGranted('approve', 'track')]
    #[Route(
        '/track/{id}/approve',
        requirements: ['id' => '\d+'],
        name: 'caldera_criticalmass_track_approve',
        methods: ['POST'],
        priority: 150
    )]
    public function approveAction(Request $request, Track $track, ManagerRegistry $registry): Response
    {
        if (!$this->isCsrfTokenValid('track_approve_' . $track->getId(), (string) $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $track->setReviewed(true);

        $registry->getManager()->flush();

        return $this->redirectToRoute('caldera_criticalmass_track_view', [
            'id' => $track->getId(),
        ]);
    }
}
