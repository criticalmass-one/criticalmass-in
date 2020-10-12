<?php declare(strict_types=1);

namespace App\Criticalmass\Corona\Model;

class Result
{
    protected ?float $latitude = null;
    protected ?float $longitude = null;
    protected ?string $districtName = null;
    protected ?float $deathRate = null;
    protected ?int $cases = null;
    protected ?int $deaths = null;
    protected ?int $recovered = null;
    protected ?float $casesPer100K = null;
    protected ?float $casesPerPopulation = null;
    protected ?float $cases7Per100K = null;
    protected ?float $cases7BlPer100K = null;
    protected ?int $population = null;
    protected ?\DateTime $lastUpdate = null;

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): Result
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): Result
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getDistrictName(): ?string
    {
        return $this->districtName;
    }

    public function setDistrictName(?string $districtName): Result
    {
        $this->districtName = $districtName;

        return $this;
    }

    public function getDeathRate(): ?float
    {
        return $this->deathRate;
    }

    public function setDeathRate(?float $deathRate): Result
    {
        $this->deathRate = $deathRate;

        return $this;
    }

    public function getCases(): ?int
    {
        return $this->cases;
    }

    public function setCases(?int $cases): Result
    {
        $this->cases = $cases;

        return $this;
    }

    public function getDeaths(): ?int
    {
        return $this->deaths;
    }

    public function setDeaths(?int $deaths): Result
    {
        $this->deaths = $deaths;

        return $this;
    }

    public function getRecovered(): ?int
    {
        return $this->recovered;
    }

    public function setRecovered(?int $recovered): Result
    {
        $this->recovered = $recovered;

        return $this;
    }

    public function getCasesPer100K(): ?float
    {
        return $this->casesPer100K;
    }

    public function setCasesPer100K(?float $casesPer100K): Result
    {
        $this->casesPer100K = $casesPer100K;

        return $this;
    }

    public function getCasesPerPopulation(): ?float
    {
        return $this->casesPerPopulation;
    }

    public function setCasesPerPopulation(?float $casesPerPopulation): Result
    {
        $this->casesPerPopulation = $casesPerPopulation;

        return $this;
    }

    public function getCases7Per100K(): ?float
    {
        return $this->cases7Per100K;
    }

    public function setCases7Per100K(?float $cases7Per100K): Result
    {
        $this->cases7Per100K = $cases7Per100K;

        return $this;
    }

    public function getCases7BlPer100K(): ?float
    {
        return $this->cases7BlPer100K;
    }

    public function setCases7BlPer100K(?float $cases7BlPer100K): Result
    {
        $this->cases7BlPer100K = $cases7BlPer100K;

        return $this;
    }

    public function getPopulation(): ?int
    {
        return $this->population;
    }

    public function setPopulation(?int $population): Result
    {
        $this->population = $population;

        return $this;
    }

    public function getLastUpdate(): ?\DateTime
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(?\DateTime $lastUpdate): Result
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }
}
