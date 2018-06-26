<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Board;
use AppBundle\Criticalmass\ViewStorage\ViewStorageCache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\City;
use AppBundle\Entity\Post;
use AppBundle\Entity\Thread;
use AppBundle\EntityInterface\BoardInterface;
use Malenki\Slug;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BoardController extends AbstractController
{
    public function overviewAction(): Response
    {
        return $this->render('AppBundle:Board:overview.html.twig', [
            'boards' => $this->getBoardRepository()->findEnabledBoards(),
            'cities' => $this->getCityRepository()->findCitiesWithBoard(),
        ]);
    }

    /**
     * @ParamConverter("city", class="AppBundle:City", isOptional="true")
     * @ParamConverter("board", class="AppBundle:Board", isOptional="true")
     */
    public function listthreadsAction(Board $board = null, City $city = null): Response
    {
        $threads = [];
        $newThreadUrl = '';

        if ($board) {
            $threads = $this->getThreadRepository()->findThreadsForBoard($board);

            $newThreadUrl = $this->generateUrl('caldera_criticalmass_board_addthread', [
                'boardSlug' => $board->getSlug(),
            ]);
        }

        if ($city) {
            $threads = $this->getThreadRepository()->findThreadsForCity($city);

            $newThreadUrl = $this->generateUrl('caldera_criticalmass_board_addcitythread', [
                'citySlug' => $city->getSlug(),
            ]);
        }

        return $this->render('AppBundle:Board:list_threads.html.twig', [
            'threads' => $threads,
            'board' => ($board ? $board : $city),
            'newThreadUrl' => $newThreadUrl,
        ]);
    }

    /**
     * @ParamConverter("thread", class="AppBundle:Thread")
     */
    public function viewthreadAction(ViewStorageCache $viewStorageCache, Thread $thread): Response
    {
        $posts = $this->getPostRepository()->findPostsForThread($thread);
        $board = $thread->getCity() ?? $thread->getBoard();

        $viewStorageCache->countView($thread);

        return $this->render('AppBundle:Board:view_thread.html.twig', [
            'board' => $board,
            'thread' => $thread,
            'posts' => $posts,
        ]);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("city", class="AppBundle:City", isOptional="true")
     * @ParamConverter("board", class="AppBundle:Board", isOptional="true")
     */
    public function addThreadAction(Request $request, Board $board = null, City $city = null): Response
    {
        $board = $board ?? $city;

        $data = [];
        $form = $this->createFormBuilder($data)
            ->add('title', TextType::class)
            ->add('message', TextareaType::class)
            ->getForm();

        if (Request::METHOD_POST === $request->getMethod()) {
            return $this->addThreadPostAction($request, $board, $form);
        } else {
            return $this->addThreadGetAction($request, $board, $form);
        }
    }

    protected function addThreadGetAction(Request $request, BoardInterface $board, Form $form): Response
    {
        return $this->render('AppBundle:Board:add_thread.html.twig', [
            'board' => $board,
            'form' => $form->createView(),
        ]);
    }

    protected function addThreadPostAction(Request $request, BoardInterface $board, Form $form): Response
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
            $thread->setSlug($slug);

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

            return $this->redirectToObject($thread);
        }

        return $this->render('AppBundle:Board:add_thread.html.twig', [
            'board' => $board,
            'form' => $form->createView(),
        ]);
    }
}
