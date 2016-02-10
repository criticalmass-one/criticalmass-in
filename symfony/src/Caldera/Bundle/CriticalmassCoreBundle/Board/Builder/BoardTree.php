<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Board\Builder;


use Caldera\Bundle\CriticalmassCoreBundle\Board\Category\CityCategory;

class BoardTree
{
    protected $root;

    public function addCategory(CityCategory $cityCategory)
    {
        $this->root[] = $cityCategory;
    }

    public function getRootNode()
    {
        return $this->root;
    }
}