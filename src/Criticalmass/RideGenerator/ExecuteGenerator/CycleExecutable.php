<?php declare(strict_types=1);

namespace App\Criticalmass\RideGenerator\ExecuteGenerator;

use App\Criticalmass\RideGenerator\Validator\Constraint\ExecutorDateTime;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ExecutorDateTime
 */
class CycleExecutable
{
    /**
     * @var \DateTime $fromDate
     * @Assert\GreaterThanOrEqual("1992-09-01", message="Vor September 1992 können keine Touren angelegt werden — das ist übrigens das Datum der allerersten Critical Mass in San Francisco.")
     */
    protected $fromDate;

    /**
     * @var \DateTime $untilDate
     * @Assert\LessThanOrEqual("+1 years", message="Touren können maximal zwölf Monate im Voraus angelegt werden.")
     */
    protected $untilDate;

    public function __construct()
    {
        $this->fromDate = new \DateTime();
        $this->untilDate = new \DateTime();
    }

    public function getFromDate(): ?\DateTime
    {
        return $this->fromDate;
    }

    public function setFromDate(\DateTime $fromDate = null): self
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    public function getUntilDate(): ?\DateTime
    {
        return $this->untilDate;
    }

    public function setUntilDate(\DateTime $untilDate = null): self
    {
        $this->untilDate = $untilDate;

        return $this;
    }
}
