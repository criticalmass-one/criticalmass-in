<?php declare(strict_types=1);

namespace App\Controller;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\Board;
use App\Event\View\ViewEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\City;
use App\Entity\Post;
use App\Entity\Thread;
use App\EntityInterface\BoardInterface;
use Malenki\Slug;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BoardController extends AbstractController
{
    public function overviewAction(): Response
    {
        return $this->render('Board/overview.html.twig', [
            'boards' => $this->getBoardRepository()->findEnabledBoards(),
            'cities' => $this->getCityRepository()->findCitiesWithBoard(),
        ]);
    }

    /**
     * @ParamConverter("city", class="App:City", isOptional="true")
     * @ParamConverter("board", class="App:Board", isOptional="true")
     */
    public function listThreadsAction(ObjectRouterInterface $objectRouter, Board $board = null, City $city = null): Response
    {
        $threads = [];
        $newThreadUrl = '';

        if ($board) {
            $threads = $this->getThreadRepository()->findThreadsForBoard($board);

            $newThreadUrl = $objectRouter->generate($board, 'caldera_criticalmass_board_addthread');
        }

        if ($city) {
            $threads = $this->getThreadRepository()->findThreadsForCity($city);

            $newThreadUrl = $objectRouter->generate($city, 'caldera_criticalmass_board_addcitythread');
        }

        return $this->render('Board/list_threads.html.twig', [
            'threads' => $threads,
            'board' => ($board ? $board : $city),
            'newThreadUrl' => $newThreadUrl,
        ]);
    }

    /**
     * @ParamConverter("thread", class="App:Thread")
     */
    public function viewThreadAction(EventDispatcherInterface $eventDispatcher, Thread $thread): Response
    {
        $posts = $this->getPostRepository()->findPostsForThread($thread);
        $board = $thread->getCity() ?? $thread->getBoard();

        $eventDispatcher->dispatch(new ViewEvent($thread), ViewEvent::NAME);

        return $this->render('Board/view_thread.html.twig', [
            'board' => $board,
            'thread' => $thread,
            'posts' => $posts,
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("city", class="App:City", isOptional="true")
     * @ParamConverter("board", class="App:Board", isOptional="true")
     */
    public function addThreadAction(Request $request, ObjectRouterInterface $objectRouter, Board $board = null, City $city = null): Response
    {
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

        if ($form->isValid()) {
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
            $post->setMessage($data['message']);
            $post->setThread($thread);
            $post->setDateTime(new \DateTime());

            $em = $this->getDoctrine()->getManager();

            $em->persist($post);
            $em->persist($thread);
            $em->persist($board);

            $em->flush();

            return $this->redirect($objectRouter->generate($thread));
        }

        return $this->render('Board/add_thread.html.twig', [
            'board' => $board,
            'form' => $form->createView(),
        ]);
    }
}
