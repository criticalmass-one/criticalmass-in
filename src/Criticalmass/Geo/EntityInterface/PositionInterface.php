<?php declare(strict_types=1);

namespace App\Criticalmass\Geo\EntityInterface;

use App\Criticalmass\Geo\Coord\CoordInterface;
use Carbon\Carbon;

/** @deprecated  */
interface PositionInterface extends CoordInterface
{
    public function setLatitude(float $latitude): PositionInterface;
    public function getLatitude(): ?float;

    public function setLongitude(float $longitude): PositionInterface;
    public function getLongitude(): ?float;

    public function setAccuracy(float $accuracy): PositionInterface;
    public function getAccuracy(): ?float;

    public function setAltitude(float $altitude): PositionInterface;
    public function getAltitude(): ?float;

    public function setAltitudeAccuracy(float $altitudeAccuracy): PositionInterface;
    public function getAltitudeAccuracy(): ?float;

    public function setHeading(float $heading): PositionInterface;
    public function getHeading(): ?float;

    public function setSpeed(float $speed): PositionInterface;
    public function getSpeed(): ?float;

    public function setTimestamp(int $timestamp): PositionInterface;
    public function getTimestamp(): ?int;

    public function setDateTime(Carbon $creationDateTime): PositionInterface;
    public function getDateTime(): ?Carbon;
}
