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

class TrackManagementController extends AbstractController
{
    /**
     * @Security("is_granted('ROLE_USER')")
     */
    public function listAction(Request $request, PaginatorInterface $paginator, UserInterface $user = null)
    {
        $query = $this->getTrackRepository()->findByUserQuery($user, null, false);

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

        $this->getManager()->flush();

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

        $this->getManager()->flush();

        $eventDispatcher->dispatch(new TrackDeletedEvent($track), TrackDeletedEvent::NAME);

        return $this->redirectToRoute('caldera_criticalmass_track_list');
    }
}
