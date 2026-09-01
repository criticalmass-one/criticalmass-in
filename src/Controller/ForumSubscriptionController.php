<?php declare(strict_types=1);

namespace App\Controller;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Board;
use App\Entity\City;
use App\Entity\ForumSubscription;
use App\Entity\Thread;
use App\Entity\User;
use App\Repository\ForumSubscriptionRepository;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class ForumSubscriptionController extends AbstractController
{
    #[Route('/forum/subscribe/thread/{threadSlug}', name: 'caldera_criticalmass_forum_subscribe_thread', methods: ['POST'], priority: 240)]
    public function threadAction(
        ObjectRouterInterface $objectRouter,
        ForumSubscriptionRepository $repository,
        #[MapEntity(mapping: ['threadSlug' => 'slug'])] Thread $thread
    ): Response {
        $this->toggle($repository, $thread, null, null, false, 'dieses Thema');

        return $this->redirect($objectRouter->generate($thread));
    }

    #[Route('/forum/subscribe/board/{boardSlug}', name: 'caldera_criticalmass_forum_subscribe_board', methods: ['POST'], priority: 240)]
    public function boardAction(
        ObjectRouterInterface $objectRouter,
        ForumSubscriptionRepository $repository,
        #[MapEntity(mapping: ['boardSlug' => 'slug'])] Board $board
    ): Response {
        $this->toggle($repository, null, $board, null, false, sprintf('das Forum „%s“', $board->getTitle()));

        return $this->redirect($objectRouter->generate($board));
    }

    #[Route('/forum/subscribe/city/{citySlug}', name: 'caldera_criticalmass_forum_subscribe_city', methods: ['POST'], priority: 240)]
    public function cityAction(
        ObjectRouterInterface $objectRouter,
        ForumSubscriptionRepository $repository,
        City $city
    ): Response {
        $this->toggle($repository, null, null, $city, false, sprintf('das Forum von %s', $city->getTitle()));

        return $this->redirect($objectRouter->generate($city, 'caldera_criticalmass_board_listcitythreads'));
    }

    #[Route('/forum/subscribe/global', name: 'caldera_criticalmass_forum_subscribe_global', methods: ['POST'], priority: 240)]
    public function globalAction(ForumSubscriptionRepository $repository): Response
    {
        $this->toggle($repository, null, null, null, true, 'das gesamte Forum');

        return $this->redirectToRoute('caldera_criticalmass_board_overview');
    }

    /**
     * Übersicht der eigenen Abonnements samt Hauptschalter für die Mails.
     */
    #[Route('/forum/subscriptions', name: 'caldera_criticalmass_forum_subscriptions', priority: 240)]
    public function listAction(Request $request, ForumSubscriptionRepository $repository): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($request->isMethod(Request::METHOD_POST)) {
            $user->setForumNotifications($request->request->getBoolean('notifications'));

            $this->managerRegistry->getManager()->flush();

            $this->addFlash('success', $user->wantsForumNotifications()
                ? 'Du bekommst wieder Benachrichtigungen aus dem Forum.'
                : 'Du bekommst keine Benachrichtigungen mehr aus dem Forum.');

            return $this->redirectToRoute('caldera_criticalmass_forum_subscriptions');
        }

        return $this->render('Forum/subscriptions.html.twig', [
            'subscriptions' => $repository->findForUser($user),
            'notificationsEnabled' => $user->wantsForumNotifications(),
        ]);
    }

    #[Route('/forum/subscriptions/{id}/remove', name: 'caldera_criticalmass_forum_unsubscribe', methods: ['POST'], priority: 240)]
    public function removeAction(ForumSubscription $subscription): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($subscription->getUser() !== $user) {
            throw $this->createAccessDeniedException('Das ist nicht dein Abonnement.');
        }

        $manager = $this->managerRegistry->getManager();
        $manager->remove($subscription);
        $manager->flush();

        $this->addFlash('success', 'Das Abonnement wurde beendet.');

        return $this->redirectToRoute('caldera_criticalmass_forum_subscriptions');
    }

    /**
     * Ein Klick schaltet an, der nächste wieder aus.
     */
    private function toggle(
        ForumSubscriptionRepository $repository,
        ?Thread $thread,
        ?Board $board,
        ?City $city,
        bool $globalScope,
        string $label
    ): void {
        /** @var User $user */
        $user = $this->getUser();

        $manager = $this->managerRegistry->getManager();
        $existing = $repository->findExisting($user, $thread, $board, $city, $globalScope);

        if (null !== $existing) {
            $manager->remove($existing);
            $manager->flush();

            $this->addFlash('success', sprintf('Du bekommst keine Benachrichtigungen mehr über %s.', $label));

            return;
        }

        $subscription = (new ForumSubscription())
            ->setUser($user)
            ->setThread($thread)
            ->setBoard($board)
            ->setCity($city)
            ->setGlobalScope($globalScope);

        $manager->persist($subscription);
        $manager->flush();

        $this->addFlash('success', sprintf('Du wirst über %s benachrichtigt.', $label));
    }
}
