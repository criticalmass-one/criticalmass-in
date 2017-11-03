<?php

namespace Criticalmass\Bundle\AppBundle\EntityInterface;

interface CoordinateInterface
{
    public function setLatitude(float $latitude = null): CoordinateInterface;

    public function getLatitude(): ?float;

    public function setLongitude(float $longitude = null): CoordinateInterface;

    public function getLongitude(): ?float;
}
