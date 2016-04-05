<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Timeline\Item;

interface ItemInterface
{
    public function setDateTime(\DateTime $dateTime);
    public function getDateTime();
}