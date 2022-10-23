<?php declare(strict_types=1);

namespace App\Model\Heatmap;

use App\Entity\City;
use App\Entity\Heatmap;

class HeatmapListModel
{
    public function __construct(protected City $city, protected Heatmap $heatmap, protected int $trackCounter, protected int $cityTrackCounter, protected \DateTime $lastUpdate)
    {
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function getHeatmap(): Heatmap
    {
        return $this->heatmap;
    }

    public function getTrackCounter(): int
    {
        return $this->trackCounter;
    }

    public function getCityTrackCounter(): int
    {
        return $this->cityTrackCounter;
    }
    
    public function getLastUpdate(): \DateTime
    {
        return $this->lastUpdate;
    }
}