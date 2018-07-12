<?php

namespace App\EntityInterface;

interface CoordinateInterface
{
    public function setLatitude(float $latitude = null): CoordinateInterface;

    public function getLatitude(): ?float;

    public function setLongitude(float $longitude = null): CoordinateInterface;

    public function getLongitude(): ?float;
}
