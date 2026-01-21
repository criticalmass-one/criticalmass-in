<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\EntityInterface;

interface TrackInterface
{
    public function getPolyline(): ?string;
    public function setPolyline(?string $polyline): static;
    public function setReducedPolyline(string $reducedPolyline = null): TrackInterface;
    public function getReducedPolyline(): ?string;
}
