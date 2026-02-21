<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\EntityInterface;

use App\Entity\TrackPolyline;
use App\Enum\PolylineResolution;

interface TrackInterface
{
    public function getPolylineByResolution(PolylineResolution $resolution): ?TrackPolyline;
    public function getPolylineString(PolylineResolution $resolution): ?string;
}
