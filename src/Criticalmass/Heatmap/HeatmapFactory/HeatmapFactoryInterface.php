<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\HeatmapFactory;

use App\Entity\City;
use App\Entity\Heatmap;
use App\Entity\Ride;
use App\Entity\User;

interface HeatmapFactoryInterface
{
    public function withCity(City $city): HeatmapFactoryInterface;
    public function withRide(Ride $ride): HeatmapFactoryInterface;
    public function setUser(User $user): HeatmapFactoryInterface;
    public function build(): Heatmap;
}
