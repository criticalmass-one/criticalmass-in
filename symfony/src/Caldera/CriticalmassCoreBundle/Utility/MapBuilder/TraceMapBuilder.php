<?php

namespace Caldera\CriticalmassCoreBundle\Utility\MapBuilder;

use Caldera\CriticalmassCoreBundle\Utility\PositionFilterChain as PositionFilterChain;
use Caldera\CriticalmassCoreBundle\Utility\MapElement as MapElement;
use Caldera\CriticalmassCoreBundle\Utility\MapBuilder\MapBuilderHelper as MapBuilderHelper;
use Caldera\CriticalmassCoreBundle\Utility as Utility;

class TraceMapBuilder extends BaseMapBuilder
{
    public function registerModules()
    {
        $coreNamespace = "\\Caldera\\CriticalmassCoreBundle\\Utility\\MapBuilderModule\\";

        $this->registerModule($coreNamespace."MapCenterMapBuilderModule");
        $this->registerModule($coreNamespace."TracePositionMapBuilderModule");
        $this->registerModule($coreNamespace."AverageSpeedMapBuilderModule");
        $this->registerModule($coreNamespace."ZoomFactorMapBuilderModule");
        $this->registerModule($coreNamespace."UserOnlineMapBuilderModule");
        $this->registerModule($coreNamespace."RideMapBuilderModule");
        $this->registerModule($coreNamespace."OtherCitiesMapBuilderModule");
    }
}
