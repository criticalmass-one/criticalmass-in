<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Board\Builder\BoardBuilder;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Board;
use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Post;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Thread;
use Caldera\Bundle\CriticalmassModelBundle\EntityInterface\BoardInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class BoardController extends AbstractController
{
    public function overviewAction(Request $request)
    {
        $boards = $this->getBoardRepository()->findEnabledBoards();

        $cities = $this->getCityRepository()->findCitiesWithBoard();

        return $this->render(
            'CalderaCriticalmassSiteBundle:Board:overview.html.twig',
            [
                'boards' => $boards,
                'cities' => $cities
            ]
        );
    }

    public function listthreadsAction(Request $request, $boardSlug = null, $citySlug = null)
    {
        $board = null;
        $city = null;
        $threads = [];

        if ($boardSlug) {
            $board = $this->getBoardRepository()->findBoardBySlug($boardSlug);

            $threads = $this->getThreadRepository()->findThreadsForBoard($board);
        }

        if ($citySlug) {
            $city = $this->getCheckedCity($citySlug);

            $threads = $this->getThreadRepository()->findThreadsForCity($city);

        }
        return $this->render(
            'CalderaCriticalmassSiteBundle:Board:listThreads.html.twig',
            [
                'threads' => $threads,
                'board' => ($board ? $board : $city)
            ]
        );
    }

    public function viewthreadAction(Request $request, $boardSlug = null, $citySlug = null, $threadSlug)
    {
        /**
         * @var BoardInterface $board
         */
        $board = null;

        if ($boardSlug) {
            $board = $this->getBoardRepository()->findBoardBySlug($boardSlug);
        }

        if ($boardSlug) {
            $board = $this->getCheckedCity($citySlug);
        }

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

    public function addthreadAction(Request $request, $boardSlug = null, $citySlug = null)
    {
        /**
         * @var BoardInterface $board
         */
        $board = null;

        if ($boardSlug) {
            $board = $this->getBoardRepository()->findBoardBySlug($slug);
        }

        if ($citySlug) {
            $board = $this->getCheckedCity($citySlug);
        }

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

    protected function addThreadGetAction(Request $request, BoardInterface $board, Form $form)
    {
        return $this->render(
            'CalderaCriticalmassSiteBundle:Board:addThread.html.twig',
            [
                'board' => $board,
                'form' => $form->createView()
            ]
        );
    }

    protected function addThreadPostAction(Request $request, BoardInterface $board, Form $form)
    {
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $thread = new Thread();
            $post = new Post();

            $slugGenerator = $this->get('caldera.criticalmass.sluggenerator');
            $slug = $slugGenerator->generate($data['title']);

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
