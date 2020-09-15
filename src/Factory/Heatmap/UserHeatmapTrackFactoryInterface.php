<?php declare(strict_types=1);

namespace App\Factory\Heatmap;

use App\Entity\Heatmap;

interface UserHeatmapTrackFactoryInterface
{
    public function generateList(Heatmap $heatmap);
}
