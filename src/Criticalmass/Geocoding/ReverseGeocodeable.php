<?php declare(strict_types=1);

namespace App\Criticalmass\Geocoding;

interface ReverseGeocodeable
{
    public function getLatitude(): ?float;
    public function getLongitude(): ?float;
    public function setLocation(string $location = null): ReverseGeocodeable;
    public function getLocation(): ?string;
}
