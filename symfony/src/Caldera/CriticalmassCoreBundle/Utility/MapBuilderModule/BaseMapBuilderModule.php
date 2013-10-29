<?php

namespace Caldera\CriticalmassCoreBundle\Utility\MapBuilderModule;

use Caldera\CriticalmassCoreBundle\Utility\MapBuilder\BaseMapBuilder as BaseMapBuilder;

class BaseMapBuilderModule
{
    protected $mapBuilder;

    public function __construct(BaseMapBuilder $mapBuilder)
    {
        $this->mapBuilder = $mapBuilder;
    }
} 