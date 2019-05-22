<?php declare(strict_types=1);

namespace App\Criticalmass\Ical;

use Sabre\VObject\Component\VCalendar;

abstract class AbstractIcalGenerator
{
    /** @var VCalendar $calendar */
    protected $calendar;

    public function __construct()
    {
        $this->calendar = new VCalendar();
    }

    public function getSerializedContent(): string
    {
        return $this->calendar->serialize();
    }
}