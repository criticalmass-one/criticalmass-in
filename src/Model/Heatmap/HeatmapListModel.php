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

    /** @var \DateTime $lastUpdate */
    protected $lastUpdate;

    public function __construct(City $city, Heatmap $heatmap, int $trackCounter, \DateTime $lastUpdate)
    {
        $this->city = $city;
        $this->heatmap = $heatmap;
        $this->trackCounter = $trackCounter;
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

    public function getLastUpdate(): \DateTime
    {
        return $this->lastUpdate;
    }
}