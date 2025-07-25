<?php declare(strict_types=1);

namespace App\Controller\Profile;

use App\Controller\AbstractController;
use App\Criticalmass\Participation\CityList\ParticipationCityListFactoryInterface;
use App\Criticalmass\Profile\ParticipationTable\TableGeneratorInterface;
use App\Criticalmass\Profile\Streak\StreakGeneratorInterface;
use App\Entity\Participation;
use App\Event\Participation\ParticipationDeletedEvent;
use App\Event\Participation\ParticipationUpdatedEvent;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ParticipationController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    public function listAction(UserInterface $user = null, ManagerRegistry $registry, TableGeneratorInterface $tableGenerator, StreakGeneratorInterface $streakGenerator, ParticipationCityListFactoryInterface $participationCityListFactory): Response
    {
        $streakGenerator->setUser($user);

        $repository = $this->managerRegistry->getRepository(Participation::class);

        $participationTable = $tableGenerator->setUser($user)->generate()->getTable();

        $participationCityList = $participationCityListFactory->buildForUser($user)->sort()->getParticipationCityList();

        return $this->render('Participation/list.html.twig', [
            'participationYesList' => $repository->findByUser($user, true),
            'participationMaybeList' => $repository->findByUser($user, false, true),
            'participationNoList' => $repository->findByUser($user, false, false, true),
            'participationTable' => $participationTable,
            'currentStreak' => $streakGenerator->calculateCurrentStreak(new \DateTime(), true),
            'longestStreak' => $streakGenerator->calculateLongestStreak(),
            'participationCityList' => $participationCityList->getList(),
        ]);
    }

    #[IsGranted('cancel', 'participation')]
    public function updateAction(
        Request $request,
        ManagerRegistry $registry,
        EventDispatcherInterface $eventDispatcher,
        Participation $participation
    ): Response {
        $status = $request->query->get('status', 'maybe');

        $participation
            ->setGoingYes($status === 'yes')
            ->setGoingMaybe($status === 'maybe')
            ->setGoingNo($status === 'no');

        $registry->getManager()->flush();

        $eventDispatcher->dispatch(new ParticipationUpdatedEvent($participation), ParticipationUpdatedEvent::NAME);

        return $this->redirectToRoute('criticalmass_user_participation_list');
    }

    #[IsGranted('delete', 'participation')]
    public function deleteAction(
        ManagerRegistry $registry,
        EventDispatcherInterface $eventDispatcher,
        Participation $participation
    ): Response {
        $registry->getManager()->remove($participation);

        $registry->getManager()->flush();

        $eventDispatcher->dispatch(new ParticipationDeletedEvent($participation), ParticipationDeletedEvent::NAME);

        return $this->redirectToRoute('criticalmass_user_participation_list');
    }
}
