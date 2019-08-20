<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap;

use App\Entity\City;
use App\Entity\Ride;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;

interface HeatmapInterface
{
    public function getIdentifier(): string;
    public function getUser(): ?User;
    public function getCity(): ?City;
    public function getRide(): ?Ride;
    public function getHeatmapTracks(): Collection;
}