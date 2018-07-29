<?php declare(strict_types=1);

namespace App\Criticalmass\Gps\PolylineGenerator;

use App\Entity\Track;

/**
 * @deprecated
 */
interface PolylineGeneratorInterface
{
    public function loadTrack(Track $track): PolylineGeneratorInterface;
    public function execute(): PolylineGeneratorInterface;
    public function getPolyline(): string;
}
