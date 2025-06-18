<?php declare(strict_types=1);

namespace App\Controller\Track;

use App\Event\Track\TrackHiddenEvent;
use App\Event\Track\TrackShownEvent;
use App\Repository\TrackRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Controller\AbstractController;
use App\Entity\Track;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TrackManagementController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    public function listAction(
        Request $request,
        TrackRepository $trackRepository,
        PaginatorInterface $paginator,
        UserInterface $user = null
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

    /**
     * @Security("is_granted('edit', track)")
     * @ParamConverter("track", class="App:Track", options={"id" = "trackId"})
     */
    public function toggleAction(EventDispatcherInterface $eventDispatcher, Track $track): Response
    {
        $track->setEnabled(!$track->getEnabled());

        $this->managerRegistry->getManager()->flush();

        if ($track->getEnabled()) {
            $eventDispatcher->dispatch(new TrackShownEvent($track), TrackShownEvent::NAME);
        } else {
            $eventDispatcher->dispatch(new TrackHiddenEvent($track), TrackHiddenEvent::NAME);
        }

        return $this->redirectToRoute('caldera_criticalmass_track_list');
    }

    /**
     * @Security("is_granted('edit', track)")
     * @ParamConverter("track", class="App:Track", options={"id" = "trackId"})
     */
    public function deleteAction(Track $track, EventDispatcherInterface $eventDispatcher): Response
    {
        $track->setDeleted(true);

        $this->managerRegistry->getManager()->flush();

        $eventDispatcher->dispatch(new TrackDeletedEvent($track), TrackDeletedEvent::NAME);

        return $this->redirectToRoute('caldera_criticalmass_track_list');
    }
}
