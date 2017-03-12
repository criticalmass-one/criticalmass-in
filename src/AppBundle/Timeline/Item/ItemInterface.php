<?php

namespace AppBundle\Timeline\Item;

interface ItemInterface
{
    public function setDateTime(\DateTime $dateTime);

    public function getDateTime();

    public function getUniqId();
}