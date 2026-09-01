<?php declare(strict_types=1);

namespace App\Controller;

use App\Criticalmass\Forum\ForumStatistics;
use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Board;
use App\Repository\BoardRepository;
use App\Repository\CityRepository;
use App\Repository\PostRepository;
use App\Repository\ThreadRepository;
use App\Entity\City;
use App\Entity\ForumSubscription;
use App\Entity\Post;
use App\Entity\Thread;
use App\EntityInterface\BoardInterface;
use App\Form\Type\ThreadType;
use Malenki\Slug;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Attribute\Route;

class BoardController extends AbstractController
{
    public const THREADS_PER_PAGE = 20;
    public const POSTS_PER_PAGE = 20;
    public const RESULTS_PER_PAGE = 20;
    public const MINIMUM_SEARCH_LENGTH = 3;

    #[Route('/boards/overview', name: 'caldera_criticalmass_board_overview', priority: 240)]
    public function overviewAction(
        CityRepository $cityRepository,
        BoardRepository $boardRepository
    ): Response
    {
        return $this->render('Board/overview.html.twig', [
            'boards' => $boardRepository->findEnabledBoards(),
            'cities' => $cityRepository->findCitiesWithBoard(),
        ]);
    }

    #[Route('/boards/search', name: 'caldera_criticalmass_board_search', priority: 260)]
    public function searchAction(
        Request $request,
        PaginatorInterface $paginator,
        PostRepository $postRepository
    ): Response {
        $term = trim((string) $request->query->get('q', ''));
        $results = null;

        // Ein einzelner Buchstabe traefe halbe Foren — erst ab drei Zeichen suchen.
        if (mb_strlen($term) >= self::MINIMUM_SEARCH_LENGTH) {
            $results = $paginator->paginate(
                $postRepository->querySearchInForum($term),
                $request->query->getInt('page', 1),
                self::RESULTS_PER_PAGE
            );
        }

        return $this->render('Board/search_results.html.twig', [
            'term' => $term,
            'results' => $results,
            'minimumLength' => self::MINIMUM_SEARCH_LENGTH,
        ]);
    }

    #[Route('/boards/{boardSlug}', name: 'caldera_criticalmass_board_listthreads', priority: 240)]
    #[Route('/{citySlug}/listthreads', name: 'caldera_criticalmass_board_listcitythreads', priority: 240)]
    public function listThreadsAction(
        Request $request,
        PaginatorInterface $paginator,
        ThreadRepository $threadRepository,
        ObjectRouterInterface $objectRouter,
        #[MapEntity(mapping: ['boardSlug' => 'slug'])] ?Board $board = null,
        ?City $city = null
    ): Response {
        if (!$board && !$city) {
            throw $this->createNotFoundException();
        }

        if ($board) {
            $query = $threadRepository->queryThreadsForBoard($board);
            $newThreadUrl = $objectRouter->generate($board, 'caldera_criticalmass_board_addthread');
            $subscribeUrl = $this->generateUrl('caldera_criticalmass_forum_subscribe_board', ['boardSlug' => $board->getSlug()]);
        } else {
            $query = $threadRepository->queryThreadsForCity($city);
            $newThreadUrl = $objectRouter->generate($city, 'caldera_criticalmass_board_addcitythread');
            $subscribeUrl = $this->generateUrl('caldera_criticalmass_forum_subscribe_city', ['citySlug' => $city->getMainSlugString()]);
        }

        $threads = $paginator->paginate($query, $request->query->getInt('page', 1), self::THREADS_PER_PAGE);

        return $this->render('Board/list_threads.html.twig', [
            'threads' => $threads,
            'board' => ($board ? $board : $city),
            'newThreadUrl' => $newThreadUrl,
            'subscribeUrl' => $subscribeUrl,
        ]);
    }

    #[Route('/boards/{boardSlug}/thread/{threadSlug}', name: 'caldera_criticalmass_board_viewthread', priority: 240)]
    #[Route('/{citySlug}/thread/{threadSlug}', name: 'caldera_criticalmass_board_viewcitythread', priority: 240)]
    public function viewThreadAction(
        Request $request,
        PaginatorInterface $paginator,
        PostRepository $postRepository,
        Thread $thread
    ): Response {
        $posts = $paginator->paginate(
            $postRepository->queryPostsForThread($thread),
            $request->query->getInt('page', 1),
            self::POSTS_PER_PAGE
        );

        $board = $thread->getCity() ?? $thread->getBoard();

        return $this->render('Board/view_thread.html.twig', [
            'board' => $board,
            'thread' => $thread,
            'posts' => $posts,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/thread/edit/{threadSlug}', name: 'caldera_criticalmass_board_editthread', priority: 240)]
    public function editThreadAction(
        Request $request,
        ObjectRouterInterface $objectRouter,
        ThreadRepository $threadRepository,
        #[MapEntity(mapping: ['threadSlug' => 'slug'])] Thread $thread
    ): Response {
        $this->denyAccessUnlessGranted('edit', $thread);

        $form = $this->createForm(ThreadType::class, $thread);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $thread->setSlug($this->uniqueThreadSlug((string) $thread->getTitle(), $thread, $threadRepository));

            $this->managerRegistry->getManager()->flush();

            $this->addFlash('success', 'Der Titel wurde geändert.');

            return $this->redirect($objectRouter->generate($thread));
        }

        return $this->render('Board/edit_thread.html.twig', [
            'board' => $thread->getCity() ?? $thread->getBoard(),
            'thread' => $thread,
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/thread/lock/{threadSlug}', name: 'caldera_criticalmass_board_lockthread', methods: ['POST'], priority: 240)]
    public function lockThreadAction(
        ObjectRouterInterface $objectRouter,
        #[MapEntity(mapping: ['threadSlug' => 'slug'])] Thread $thread
    ): Response {
        $this->denyAccessUnlessGranted('lock', $thread);

        $thread->setLocked(!$thread->isLocked());

        $this->managerRegistry->getManager()->flush();

        $this->addFlash('success', $thread->isLocked()
            ? 'Das Thema ist geschlossen und nimmt keine Antworten mehr an.'
            : 'Das Thema ist wieder offen für Antworten.');

        return $this->redirect($objectRouter->generate($thread));
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/thread/pin/{threadSlug}', name: 'caldera_criticalmass_board_pinthread', methods: ['POST'], priority: 240)]
    public function pinThreadAction(
        ObjectRouterInterface $objectRouter,
        #[MapEntity(mapping: ['threadSlug' => 'slug'])] Thread $thread
    ): Response {
        $this->denyAccessUnlessGranted('pin', $thread);

        $thread->setSticky(!$thread->isSticky());

        $this->managerRegistry->getManager()->flush();

        $this->addFlash('success', $thread->isSticky()
            ? 'Das Thema steht jetzt oben in der Liste.'
            : 'Das Thema steht wieder in der normalen Reihenfolge.');

        return $this->redirect($objectRouter->generate($thread));
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/thread/move/{threadSlug}', name: 'caldera_criticalmass_board_movethread', priority: 240)]
    public function moveThreadAction(
        Request $request,
        ObjectRouterInterface $objectRouter,
        BoardRepository $boardRepository,
        CityRepository $cityRepository,
        ForumStatistics $forumStatistics,
        #[MapEntity(mapping: ['threadSlug' => 'slug'])] Thread $thread
    ): Response {
        $this->denyAccessUnlessGranted('move', $thread);

        $targets = [];

        foreach ($boardRepository->findEnabledBoards() as $candidate) {
            $targets['Foren'][(string) $candidate->getTitle()] = 'board:' . $candidate->getId();
        }

        foreach ($cityRepository->findCitiesWithBoard() as $candidate) {
            $targets['Städte'][(string) $candidate->getTitle()] = 'city:' . $candidate->getId();
        }

        $form = $this->createFormBuilder()
            ->add('target', ChoiceType::class, [
                'label' => 'Neues Forum',
                'choices' => $targets,
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            [$kind, $id] = explode(':', (string) $form->get('target')->getData(), 2);

            $target = 'city' === $kind
                ? $cityRepository->find((int) $id)
                : $boardRepository->find((int) $id);

            if (!$target instanceof BoardInterface) {
                throw $this->createNotFoundException('Dieses Forum gibt es nicht.');
            }

            $source = $thread->getCity() ?? $thread->getBoard();

            if ($source instanceof BoardInterface && $source !== $target) {
                $forumStatistics->moveThread($thread, $source, $target);

                if ($target instanceof City) {
                    $thread->setCity($target)->setBoard(null);
                } elseif ($target instanceof Board) {
                    $thread->setBoard($target)->setCity(null);
                }

                $this->managerRegistry->getManager()->flush();

                $this->addFlash('success', sprintf('Das Thema liegt jetzt in „%s“.', $target->getTitle()));
            }

            return $this->redirect($objectRouter->generate($thread));
        }

        return $this->render('Board/move_thread.html.twig', [
            'board' => $thread->getCity() ?? $thread->getBoard(),
            'thread' => $thread,
            'form' => $form->createView(),
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/thread/disable/{threadSlug}', name: 'caldera_criticalmass_board_disablethread', methods: ['POST'], priority: 240)]
    public function disableThreadAction(
        ObjectRouterInterface $objectRouter,
        ForumStatistics $forumStatistics,
        PostRepository $postRepository,
        #[MapEntity(mapping: ['threadSlug' => 'slug'])] Thread $thread
    ): Response {
        $this->denyAccessUnlessGranted('delete', $thread);

        $board = $thread->getCity() ?? $thread->getBoard();

        if ($board instanceof BoardInterface) {
            $forumStatistics->disableThread($thread, $board);
        }

        foreach ($postRepository->findPostsForThread($thread) as $post) {
            $post->getUser()?->decForumPostCount();
        }

        $thread->setEnabled(false);

        $this->managerRegistry->getManager()->flush();

        $this->addFlash('success', 'Das Thema wurde zurückgezogen.');

        return $this->redirect($board instanceof BoardInterface
            ? $objectRouter->generate($board)
            : $this->generateUrl('caldera_criticalmass_board_overview'));
    }

    /**
     * Der Slug wandert mit dem Titel mit. Weil er die Adresse des Themas ist, darf er
     * kein zweites Mal vorkommen — sonst liefert findThreadBySlug() mehr als ein Ergebnis.
     */
    protected function uniqueThreadSlug(string $title, Thread $thread, ThreadRepository $threadRepository): string
    {
        $base = (new Slug($title))->render();
        $slug = $base;
        $suffix = 1;

        while (null !== ($existing = $threadRepository->findOneBy(['slug' => $slug])) && $existing !== $thread) {
            $slug = sprintf('%s-%d', $base, ++$suffix);
        }

        return $slug;
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/boards/{boardSlug}/addthread', name: 'caldera_criticalmass_board_addthread', priority: 240)]
    #[Route('/{citySlug}/addthread', name: 'caldera_criticalmass_board_addcitythread', priority: 240)]
    public function addThreadAction(
        Request $request,
        ObjectRouterInterface $objectRouter,
        #[MapEntity(mapping: ['boardSlug' => 'slug'])] ?Board $board = null,
        ?City $city = null
    ): Response {
        $board = $board ?? $city;

        $data = [];
        $form = $this->createFormBuilder($data)
            ->add('title', TextType::class)
            ->add('message', TextareaType::class)
            ->getForm();

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->addThreadPostAction($request, $objectRouter, $board, $form);
        } else {
            return $this->addThreadGetAction($request, $objectRouter, $board, $form);
        }
    }

    protected function addThreadGetAction(Request $request, ObjectRouterInterface $objectRouter, BoardInterface $board, FormInterface $form): Response
    {
        return $this->render('Board/add_thread.html.twig', [
            'board' => $board,
            'form' => $form->createView(),
        ]);
    }

    protected function addThreadPostAction(Request $request, ObjectRouterInterface $objectRouter, BoardInterface $board, FormInterface $form): Response
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $thread = new Thread();
            $post = new Post();

            $slug = new Slug($data['title']);

            /* Okay, this is _really_ ugly */
            if ($board instanceof City) {
                $thread->setCity($board);
            } else {
                $thread->setBoard($board);
            }

            $thread->setTitle($data['title']);
            $thread->setFirstPost($post);
            $thread->setLastPost($post);
            $thread->setSlug($slug->render());

            $board->setLastThread($thread);
            $board->incPostNumber();
            $board->incThreadNumber();

            $post->setUser($this->getUser());
            $post->getUser()?->incForumPostCount();
            $post->setMessage($data['message']);
            $post->setThread($thread);
            $post->setDateTime(new \DateTime());

            $em = $this->managerRegistry->getManager();

            $em->persist($post);
            $em->persist($thread);
            $em->persist($board);

            $em->flush();

            // Wer ein Thema eroeffnet, verfolgt es in aller Regel weiter.
            $subscription = (new ForumSubscription())
                ->setUser($post->getUser())
                ->setThread($thread);

            $em->persist($subscription);
            $em->flush();

            return $this->redirect($objectRouter->generate($thread));
        }

        return $this->render('Board/add_thread.html.twig', [
            'board' => $board,
            'form' => $form->createView(),
        ]);
    }
}
