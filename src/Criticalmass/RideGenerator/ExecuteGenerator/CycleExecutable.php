<?php declare(strict_types=1);

namespace App\Criticalmass\RideGenerator\ExecuteGenerator;

use App\Criticalmass\RideGenerator\Validator\Constraint\ExecutorDateTime;

/**
 * @ExecutorDateTime
 */
class CycleExecutable
{
    /** @var \DateTime $fromDate */
    protected $fromDate;

    /** @var \DateTime $untilDate */
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
