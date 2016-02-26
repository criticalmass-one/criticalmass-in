<?php

namespace Caldera\Bundle\CriticalmassSiteBundle\Controller;

use Caldera\Bundle\CriticalmassCoreBundle\Board\Builder\BoardBuilder;
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

    public function viewcitythreadAction(Request $request, $citySlug, $threadId)
    {
        $city = $this->getCheckedCity($citySlug);

        $thread = $this->getThreadRepository()->find($threadId);
        $posts = $this->getPostRepository()->getPostsForThread($thread);

        return $this->render(
            'CalderaCriticalmassSiteBundle:Board:viewThread.html.twig',
            [
                'city' => $city,
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

    public function addcitythreadAction(Request $request, $citySlug)
    {
        $city = $this->getCheckedCity($citySlug);

        $data = [];
        $form = $this->createFormBuilder($data)
            ->add('title', 'text')
            ->add('message', 'textarea')
            ->getForm();

        if ('POST' == $request->getMethod()) {
            return $this->addCityThreadPostAction($request, $city, $form);
        } else {
            return $this->addCityThreadGetAction($request, $city, $form);
        }
    }

    protected function addCityThreadGetAction(Request $request, City $city, Form $form)
    {
        return $this->render(
            'CalderaCriticalmassSiteBundle:Board:addCityThread.html.twig',
            [
                'city' => $city,
                'form' => $form->createView()
            ]
        );
    }

    protected function addCityThreadPostAction(Request $request, City $city, Form $form)
    {
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            $thread = new Thread();
            $post = new Post();

            $thread->setCity($city);
            $thread->setTitle($data['title']);
            $thread->setFirstPost($post);
            $thread->setLastPost($post);

            $post->setUser($this->getUser());
            $post->setMessage($data['message']);
            $post->setThread($thread);
            $post->setDateTime(new \DateTime());

            $em = $this->getDoctrine()->getManager();

            $em->persist($thread);
            $em->persist($post);

            $em->flush();

            return $this->redirectToRoute(
                'caldera_criticalmass_board_citythread',
                [
                    'citySlug' => $city->getMainSlugString(),
                    'threadId' => $thread->getId()
                ]
            );
        }

        return $this->render(
            'CalderaCriticalmassSiteBundle:Board:addCityThread.html.twig',
            [
                'city' => $city,
                'form' => $form->createView()
            ]
        );
    }
}
