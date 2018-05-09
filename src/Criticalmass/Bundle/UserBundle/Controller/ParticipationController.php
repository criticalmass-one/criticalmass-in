<?php declare(strict_types=1);

namespace Criticalmass\Bundle\UserBundle\Controller;

use Criticalmass\Bundle\AppBundle\Entity\Participation;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Event\Participation\ParticipationDeletedEvent;
use Criticalmass\Bundle\AppBundle\Event\Participation\ParticipationUpdatedEvent;
use Criticalmass\Bundle\AppBundle\Repository\ParticipationRepository;
use Criticalmass\Component\Participation\Calculator\RideParticipationCalculatorInterface;
use Criticalmass\Component\Profile\Streak\StreakGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Criticalmass\Component\Profile\ParticipationTable\TableGeneratorInterface;

class ParticipationController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction(UserInterface $user, RegistryInterface $registry, TableGeneratorInterface $tableGenerator, StreakGeneratorInterface $streakGenerator): Response
    {
        $streakGenerator->setUser($user);

        $repository = $this->getDoctrine()->getRepository(Participation::class);

        $participationTable = $tableGenerator->setUser($user)->generate()->getTable();

        return $this->render('UserBundle:Participation:list.html.twig', [
            'participationYesList' => $repository->findByUser($user, true),
            'participationMaybeList' => $repository->findByUser($user, false, true),
            'participationNoList' => $repository->findByUser($user, false, false, true),
            'participationTable' => $participationTable,
            'currentStreak' => $streakGenerator->calculateCurrentStreak(new \DateTime(), true),
            'longestStreak' => $streakGenerator->calculateLongestStreak(),
        ]);
    }

    /**
     * @Security("is_granted('cancel', participation)")
     * @ParamConverter("participation", class="AppBundle:Participation", options={"id": "participationId"})
     */
    public function updateAction(Request $request, RegistryInterface $registry, EventDispatcherInterface $eventDispatcher, Participation $participation): Response
    {
        $status = $request->query->get('status', 'maybe');

        $participation
            ->setGoingYes($status === 'yes')
            ->setGoingMaybe($status === 'maybe')
            ->setGoingNo($status === 'no');

        $registry->getManager()->flush();

        $eventDispatcher->dispatch(ParticipationUpdatedEvent::NAME, new ParticipationUpdatedEvent($participation));

        return $this->redirectToRoute('criticalmass_user_participation_list');
    }

    /**
     * @Security("is_granted('delete', participation)")
     * @ParamConverter("participation", class="AppBundle:Participation", options={"id": "participationId"})
     */
    public function deleteAction(RegistryInterface $registry, EventDispatcherInterface $eventDispatcher,  Participation $participation): Response
    {
        $registry->getManager()->remove($participation);

        $registry->getManager()->flush();

        $eventDispatcher->dispatch(ParticipationDeletedEvent::NAME, new ParticipationDeletedEvent($participation));

        return $this->redirectToRoute('criticalmass_user_participation_list');
    }
}
