<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Item;

interface ItemInterface
{
    public function setDateTime(\DateTime $dateTime);

    public function getDateTime(): \DateTime;

    public function getUniqId(): string;

    public function getTabName(): string;
}
