<?php declare(strict_types=1);

namespace App\Model\Heatmap;

use App\Entity\City;
use App\Entity\Heatmap;

class HeatmapListModel
{
    /** @var City $city */
    protected $city;

    /** @var Heatmap $heatmap */
    protected $heatmap;

    /** @var int $trackCounter */
    protected $trackCounter;

    /** @var int $cityTrackCounter */
    protected $cityTrackCounter;

    /** @var \DateTime $lastUpdate */
    protected $lastUpdate;

    public function __construct(City $city, Heatmap $heatmap, int $trackCounter, int $cityTrackCounter, \DateTime $lastUpdate)
    {
        $this->city = $city;
        $this->heatmap = $heatmap;
        $this->trackCounter = $trackCounter;
        $this->cityTrackCounter = $cityTrackCounter;
        $this->lastUpdate = $lastUpdate;
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