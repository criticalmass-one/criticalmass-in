<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Controller\AbstractController;
use App\Entity\Track;
use App\Event\Track\TrackDeletedEvent;
use App\Event\Track\TrackHiddenEvent;
use App\Event\Track\TrackShownEvent;
use App\Repository\TrackRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/track', name: 'caldera_criticalmass_track_')]
class TrackManagementController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/list', name: 'list', methods: ['GET'], priority: 180)]
    public function listAction(
        Request $request,
        TrackRepository $trackRepository,
        PaginatorInterface $paginator,
        ?UserInterface $user = null
    ): Response {
        $query = $trackRepository->findByUserQuery($user, null, false);

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('Track/list.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[IsGranted('edit', 'track')]
    #[Route('/{id}/toggle', name: 'toggle', methods: ['POST'], priority: 150)]
    public function toggleAction(Request $request, EventDispatcherInterface $eventDispatcher, Track $track): Response
    {
        if (!$this->isCsrfTokenValid('track_toggle_' . $track->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $track->setEnabled(!$track->getEnabled());

        $this->managerRegistry->getManager()->flush();

        if ($track->getEnabled()) {
            $eventDispatcher->dispatch(new TrackShownEvent($track), TrackShownEvent::NAME);
        } else {
            $eventDispatcher->dispatch(new TrackHiddenEvent($track), TrackHiddenEvent::NAME);
        }

        return $this->redirectToRoute('caldera_criticalmass_track_list');
    }

    #[IsGranted('edit', 'track')]
    #[Route('/{id}/delete', name: 'delete', methods: ['POST'], priority: 150)]
    public function deleteAction(Request $request, Track $track, EventDispatcherInterface $eventDispatcher): Response
    {
        if (!$this->isCsrfTokenValid('track_delete_' . $track->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        $track->setDeleted(true);

        $this->managerRegistry->getManager()->flush();

        $eventDispatcher->dispatch(new TrackDeletedEvent($track), TrackDeletedEvent::NAME);

        return $this->redirectToRoute('caldera_criticalmass_track_list');
    }
}
