<?php declare(strict_types=1);

namespace App\Criticalmass\Heatmap\Remover;

use App\Entity\Heatmap;

interface HeatmapRemoverInterface
{
    public function remove(Heatmap $heatmap): HeatmapRemoverInterface;
    public function flush(Heatmap $heatmap): HeatmapRemoverInterface;
}
