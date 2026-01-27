<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

use Carbon\Carbon;

interface ItemInterface
{
    public function setDateTime(Carbon $dateTime);

    public function getDateTime(): Carbon;

    public function getUniqId(): string;

    public function getTabName(): string;
}
