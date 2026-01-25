<?php declare(strict_types=1);

namespace App\Model\RideGenerator;

use App\Entity\City;
use App\Entity\CityCycle;
use Carbon\Carbon;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;

class CycleExecutable
{
    /**
     * @JMS\Expose()
     */
    protected ?string $citySlug = null;

    /**
     * @JMS\Expose()
     */
    protected ?City $city = null;

    /**
     * @JMS\Expose()
     */
    protected ?CityCycle $cityCycle = null;

    /**
     * @JMS\Expose()
     * @JMS\Type("DateTime<'U'>")
     */
    #[Assert\GreaterThanOrEqual('1992-09-01', message: 'Vor September 1992 können keine Touren angelegt werden — das ist übrigens das Datum der allerersten Critical Mass in San Francisco.')]
    protected ?\DateTime $fromDate = null;

    /**
     * @JMS\Expose()
     * @JMS\Type("DateTime<'U'>")
     */
    #[Assert\LessThanOrEqual('+1 years', message: 'Touren können maximal zwölf Monate im Voraus angelegt werden.')]
    protected ?\DateTime $untilDate = null;

    public function __construct()
    {
        $this->fromDate = new Carbon();
        $this->untilDate = new Carbon();
    }

    public function getFromDate(): ?\DateTime
    {
        return $this->fromDate;
    }

    public function setFromDate(?\DateTime $fromDate = null): self
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    public function getUntilDate(): ?\DateTime
    {
        return $this->untilDate;
    }

    public function setUntilDate(?\DateTime $untilDate = null): self
    {
        $this->untilDate = $untilDate;

        return $this;
    }

    public function setCitySlug(?string $citySlug): self
    {
        $this->citySlug = $citySlug;

        return $this;
    }

    public function getCitySlug(): ?string
    {
        return $this->citySlug;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCityCycle(): ?CityCycle
    {
        return $this->cityCycle;
    }

    public function setCityCycle(?CityCycle $cityCycle): self
    {
        $this->cityCycle = $cityCycle;

        return $this;
    }
}
