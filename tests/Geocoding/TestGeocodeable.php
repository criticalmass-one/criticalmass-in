<?php declare(strict_types=1);

namespace Tests\Geocoding;

use App\Criticalmass\Geocoding\ReverseGeocodeable;

class TestGeocodeable implements ReverseGeocodeable
{
    /** @var float $latitude */
    protected $latitude;

    /** @var float $longitude */
    protected $longitude;

    /** @var string $location */
    protected $location;

    public function setLatitude(float $latitude): TestGeocodeable
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLongitude(float $longitude): TestGeocodeable
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLocation(string $location = null): ReverseGeocodeable
    {
        $this->location = $location;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }
}