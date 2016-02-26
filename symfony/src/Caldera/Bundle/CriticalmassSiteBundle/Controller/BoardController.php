<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Board\Builder\BoardBuilder;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Board;
use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Post;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Thread;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class BoardController extends AbstractController
{
    public function overviewAction(Request $request)
    {
        $boards = $this->getBoardRepository()->findEnabledBoards();

        return $this->render(
            'CalderaCriticalmassSiteBundle:Board:overview.html.twig',
            [
                'boards' => $boards
            ]
        );
    }

    public function listthreadsAction(Request $request, $slug)
    {
        $board = $this->getBoardRepository()->findBoardBySlug($slug);

        $threads = $this->getThreadRepository()->findThreadsForBoard($board);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Board:listThreads.html.twig',
            [
                'threads' => $threads,
                'board' => $board
            ]
        );
    }

    public function viewthreadAction(Request $request, $boardSlug, $threadSlug)
    {
        $board = $this->getBoardRepository()->findBoardBySlug($boardSlug);
        $thread = $this->getThreadRepository()->findThreadBySlug($threadSlug);
        $posts = $this->getPostRepository()->findPostsForThread($thread);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Board:viewThread.html.twig',
            [
                'board' => $board,
                'thread' => $thread,
                'posts' => $posts
            ]
        );
    }

    public function viewrideboardAction(Request $request, $citySlug)
    {
        $city = $this->getCheckedCity($citySlug);

        /**
         * @var BoardBuilder $boardBuilder
         */
        $boardBuilder = $this->get('caldera.criticalmass.board.builder.boardbuilder');

        $boardBuilder->buildRideBoard($city);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Board:viewRideBoard.html.twig',
            [
                'city' => $city,
                'threads' => $boardBuilder->getList()
            ]
        );
    }

    public function viewridethreadAction(Request $request, $citySlug, $rideDate)
    {
        $ride = $this->getCheckedCitySlugRideDateRide($citySlug, $rideDate);

        /**
         * @var BoardBuilder $boardBuilder
         */
        $boardBuilder = $this->get('caldera.criticalmass.board.builder.boardbuilder');

        $boardBuilder->buildRideThread($ride);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Board:viewRideThread.html.twig',
            [
                'posts' => $boardBuilder->getList()
            ]
        );
    }

    public function addthreadAction(Request $request, $slug)
    {
        $board = $this->getBoardRepository()->findBoardBySlug($slug);

        $data = [];
        $form = $this->createFormBuilder($data)
            ->add('title', 'text')
            ->add('message', 'textarea')
            ->getForm();

        if ('POST' == $request->getMethod()) {
            return $this->addThreadPostAction($request, $board, $form);
        } else {
            return $this->addThreadGetAction($request, $board, $form);
        }
    }

    protected function addThreadGetAction(Request $request, Board $board, Form $form)
    {
        return $this->render(
            'CalderaCriticalmassSiteBundle:Board:addThread.html.twig',
            [
                'board' => $board,
                'form' => $form->createView()
            ]
        );
    }

    protected function addThreadPostAction(Request $request, Board $board, Form $form)
    {
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $thread = new Thread();
            $post = new Post();

            $slugGenerator = $this->get('caldera.criticalmass.sluggenerator');
            $slug = $slugGenerator->generate($data['title']);

            $thread->setBoard($board);
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

            return $this->redirectToRoute($thread);
        }

        return $this->render(
            'CalderaCriticalmassSiteBundle:Board:addThread.html.twig',
            [
                'board' => $board,
                'form' => $form->createView()
            ]
        );
    }
}
