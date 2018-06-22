<?php declare(strict_types=1);

namespace Criticalmass\Component\Gps\TrackPolyline;

use Criticalmass\Bundle\AppBundle\Entity\Track;

interface PolylineGeneratorInterface
{
    public function loadTrack(Track $track): PolylineGeneratorInterface;
    public function execute(): PolylineGeneratorInterface;
    public function getPolyline(): string;
}
