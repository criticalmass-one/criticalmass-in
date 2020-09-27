<?php

namespace App\Model;

use JMS\Serializer\Annotation as JMS;

/**
 * @JMS\ExclusionPolicy("all")
 */
class CreateEstimateModel
{
    /**
     * @JMS\Expose()
     * @JMS\Type("DateTime<'U'>")
     */
    protected ?\DateTime $dateTime = null;

    /**
     * @JMS\Expose()
     * @JMS\Type("string")
     */
    protected ?string $citySlug = null;

    /**
     * @JMS\Expose()
     * @JMS\Type("float")
     */
    protected ?float $latitude = null;

    /**
     * @JMS\Expose()
     * @JMS\Type("float")
     */
    protected ?float $longitude = null;

    /**
     * @JMS\Expose()
     * @JMS\Type("integer")
     */
    protected ?int $estimation = null;

    /**
     * @JMS\Expose()
     * @JMS\Type("string")
     */
    protected ?string $source = null;

    public function __construct(
        \DateTime $dateTime = null,
        string $citySlug = null,
        float $latitude = null,
        float $longitude = null,
        int $estimation
    ) {
        $this->dateTime = $dateTime;
        $this->citySlug = $citySlug;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->estimation = $estimation;
    }

    public function setDateTime(\DateTime $dateTime): CreateEstimateModel
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getDateTime(): ?\DateTime
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
