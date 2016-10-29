<?php

namespace Caldera\Bundle\CalderaBundle\MapPrinter\Element;

interface TrackInterface extends MapElement
{
    public function getPolyline();
}