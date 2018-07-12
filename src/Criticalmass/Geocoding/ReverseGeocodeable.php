<?php declare(strict_types=1);

namespace App\Criticalmass\Geocoding;

interface ReverseGeocodeable
{
    public function getLatitude(): ?float;
    public function getLongitude(): ?float;
    public function setLocation(string $location): ReverseGeocodeable;
    public function getLocation(): ?string;
}
