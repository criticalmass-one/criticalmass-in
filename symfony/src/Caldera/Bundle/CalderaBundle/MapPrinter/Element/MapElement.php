<?php

namespace Caldera\Bundle\CalderaBundle\MapPrinter\Element;

interface MapElement
{
    public function getLatitude();
    public function getLongitude();
}