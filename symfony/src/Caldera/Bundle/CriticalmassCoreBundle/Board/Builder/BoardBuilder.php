<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Board\Builder;

use Caldera\Bundle\CriticalmassCoreBundle\Board\Board\CityImageCommentBoard;
use Caldera\Bundle\CriticalmassCoreBundle\Board\Board\CityRideBoard;
use Caldera\Bundle\CriticalmassCoreBundle\Board\Board\CityTalkBoard;
use Caldera\Bundle\CriticalmassCoreBundle\Board\Category\CityCategory;
use Caldera\Bundle\CriticalmassModelBundle\Repository\CityRepository;

class BoardBuilder
{
    protected $doctrine;
    protected $list = [];

    public function __construct($doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function buildOverview()
    {
        /**
         * @var CityRepository $cityRepository
         */
        $cityRepository = $this->doctrine->getRepository('CalderaCriticalmassModelBundle:City');

        $cities = $cityRepository->findCities();

        foreach ($cities as $city) {
            $category = new CityCategory();
            $category->setCity($city);

            $board = new CityTalkBoard();
            $board->setCity($city);
            $category->addBoard($board);

            $board = new CityRideBoard();
            $board->setCity($city);
            $category->addBoard($board);

            $board = new CityImageCommentBoard();
            $board->setCity($city);
            $category->addBoard($board);

            $this->list[] = $category;
        }
    }

    public function getList()
    {
        return $this->list;
    }
}