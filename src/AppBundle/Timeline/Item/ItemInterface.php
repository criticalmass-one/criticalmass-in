<?php

namespace Caldera\Bundle\CalderaBundle\Timeline\Item;

interface ItemInterface
{
    public function setDateTime(\DateTime $dateTime);

    public function getDateTime();

    public function getUniqId();
}