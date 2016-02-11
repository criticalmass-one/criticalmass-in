<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Board\Builder;

use Caldera\Bundle\CriticalmassCoreBundle\Board\Board\CityImageCommentBoard;
use Caldera\Bundle\CriticalmassCoreBundle\Board\Board\CityRideBoard;
use Caldera\Bundle\CriticalmassCoreBundle\Board\Thread\CityRideThread;
use Caldera\Bundle\CriticalmassCoreBundle\Board\Board\CityTalkBoard;
use Caldera\Bundle\CriticalmassCoreBundle\Board\Category\CityCategory;
use Caldera\Bundle\CriticalmassModelBundle\Entity\City;
use Caldera\Bundle\CriticalmassModelBundle\Repository\CityRepository;
use Caldera\Bundle\CriticalmassModelBundle\Repository\PostRepository;
use Caldera\Bundle\CriticalmassModelBundle\Repository\RideRepository;

class BoardBuilder
{
    protected $doctrine;
    protected $list = [];

    /**
     * @var CityRepository $cityRepository
     */
    protected $cityRepository;

    /**
     * @var RideRepository $rideRepository
     */
    protected $rideRepository;

    /**
     * @var PostRepository $postRepository
     */
    protected $postRepository;


    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;

        $this->initRepositories();
    }

    protected function initRepositories()
    {
        $this->cityRepository = $this->doctrine->getRepository('CalderaCriticalmassModelBundle:City');
        $this->rideRepository = $this->doctrine->getRepository('CalderaCriticalmassModelBundle:Ride');
        $this->postRepository = $this->doctrine->getRepository('CalderaCriticalmassModelBundle:Post');
    }

    public function buildOverview()
    {
        $cities = $this->cityRepository->findCitiesWithBoard();

        foreach ($cities as $city) {
            $category = new CityCategory();
            $category->setCity($city);

            $board = new CityTalkBoard();
            $board->setCity($city);
            $category->addBoard($board);

            $rides = $this->rideRepository->findRidesForCity($city);
            $posts = $this->postRepository->getPostsForCityRides($city);
            $board = new CityRideBoard();
            $board->setCity($city);
            $board->setRides($rides);
            $board->setPosts($posts);
            $category->addBoard($board);

            $board = new CityImageCommentBoard();
            $board->setCity($city);
            $category->addBoard($board);

            $this->list[] = $category;
        }
    }

    public function buildRideBoard(City $city)
    {
        $rides = $this->rideRepository->findRidesForCity($city);

        foreach ($rides as $ride) {
            $thread = new CityRideThread();

            $thread->setRide($ride);

            $this->list[] = $thread;
        }
    }

    public function getList()
    {
        return $this->list;
    }

}