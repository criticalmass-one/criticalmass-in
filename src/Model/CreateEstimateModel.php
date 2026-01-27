<?php declare(strict_types=1);

namespace App\Model;

use Carbon\Carbon;

class CreateEstimateModel
{
    protected ?Carbon $dateTime = null;

    protected ?string $citySlug = null;

    protected ?float $latitude = null;

    protected ?float $longitude = null;

    protected ?int $estimation = null;

    protected ?string $source = null;

    public function __construct(
        int $estimation,
        ?Carbon $dateTime = null,
        ?string $citySlug = null,
        ?float $latitude = null,
        ?float $longitude = null
    ) {
        $this->estimation = $estimation;
        $this->dateTime = $dateTime;
        $this->citySlug = $citySlug;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function setDateTime(Carbon $dateTime): CreateEstimateModel
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getDateTime(): ?Carbon
    {
        return $this->dateTime;
    }

    public function setCitySlug(string $citySlug): CreateEstimateModel
    {
        $this->citySlug = $citySlug;

        return $this;
    }

    public function getCitySlug(): ?string
    {
        return $this->citySlug;
    }

    public function setLatitude(float $latitude): CreateEstimateModel
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLongitude(float $longitude): CreateEstimateModel
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setEstimation(int $estimation): CreateEstimateModel
    {
        $this->estimation = $estimation;

        return $this;
    }

    public function getEstimation(): int
    {
        return $this->estimation;
    }

    public function setSource(string $source): CreateEstimateModel
    {
        $this->source = $source;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }
}
