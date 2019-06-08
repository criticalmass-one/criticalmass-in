<?php declare(strict_types=1);

namespace App\Factory\City;

use App\Entity\City;
use App\Entity\Region;
use Caldera\GeoBasic\Coord\CoordInterface;
use Symfony\Component\Security\Core\User\UserInterface;

interface CityFactoryInterface
{
    public function withColors(int $red, int $green, int $blue): CityFactoryInterface;
    public function withRandomColors(): CityFactoryInterface;
    public function withCoord(CoordInterface $coord): CityFactoryInterface;
    public function withLatitude(float $latitude): CityFactoryInterface;
    public function withLongitude(float $longitude): CityFactoryInterface;
    public function withEnabled(bool $enabled): CityFactoryInterface;
    public function withDateTimezone(\DateTimeZone $dateTimeZone): CityFactoryInterface;
    public function withTimezone(string $timezone): CityFactoryInterface;
    public function withRideNamer(string $rideNamerFqcn): CityFactoryInterface;
    public function withCreatedAt(\DateTime $createdAt): CityFactoryInterface;
    public function withRegion(Region $region): CityFactoryInterface;
    public function withUser(UserInterface $user): CityFactoryInterface;
    public function build(): City;
}