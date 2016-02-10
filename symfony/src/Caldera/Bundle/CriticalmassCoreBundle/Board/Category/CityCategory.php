<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Board\Category;

use Caldera\Bundle\CriticalmassCoreBundle\Board\Board\BoardInterface;
use Caldera\Bundle\CriticalmassModelBundle\Entity\City;

class CityCategory
{
    /**
     * @var City $city
     */
    protected $city;

    protected $boards = [];

    public function __construct()
    {

    }

    public function setCity(City $city)
    {
        $this->city = $city;
    }

    public function getTitle()
    {
        return $this->city->getCity();
    }

    public function addBoard(BoardInterface $board)
    {
        $this->boards[] = $board;
    }

    public function getBoards()
    {
        return $this->boards;
    }
}