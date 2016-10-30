<?php

namespace Caldera\Bundle\CalderaBundle\MapPrinter\Element;

interface MarkerInterface extends MapElement
{
    public function getLatitude();
    public function getLongitude();
}