<?php

namespace Criticalmass\Bundle\AppBundle\Timeline\Item;

interface ItemInterface
{
    public function setDateTime(\DateTime $dateTime);

    public function getDateTime(): \DateTime;

    public function getUniqId(): string;
}
