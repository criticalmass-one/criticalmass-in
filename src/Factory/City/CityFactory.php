<?php declare(strict_types=1);

namespace App\Factory\City;

use App\Entity\City;
use App\Entity\Region;
use Caldera\GeoBasic\Coord\CoordInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class CityFactory implements CityFactoryInterface
{
    /**
     * @var City $city
     */
    protected $city;

    public function __construct()
    {
        $this->city = new City();
    }

    public function withColors(int $red, int $green, int $blue): CityFactoryInterface
    {
        $this->city
            ->setColorRed($red)
            ->setColorGreen($green)
            ->setColorBlue($blue);

        return $this;
    }

    public function withRandomColors(): CityFactoryInterface
    {
        $red = random_int(0, 255);
        $green = random_int(0, 255);
        $blue = random_int(0, 255);

        $this->withColors($red, $green, $blue);

        return $this;
    }

    public function withCoord(CoordInterface $coord): CityFactoryInterface
    {
        $this
            ->withLatitude($coord->getLatitude())
            ->withLongitude($coord->getLongitude());

        return $this;
    }

    public function withLatitude(float $latitude): CityFactoryInterface
    {
        $this->city->setLatitude($latitude);

        return $this;
    }

    public function withLongitude(float $longitude): CityFactoryInterface
    {
        $this->city->setLongitude($longitude);

        return $this;
    }

    public function withEnabled(bool $enabled): CityFactoryInterface
    {
        $this->city->setEnabled($enabled);

        return $this;
    }

    public function withDateTimezone(\DateTimeZone $dateTimeZone): CityFactoryInterface
    {
        $this->withTimezone($dateTimeZone->getName());

        return $this;
    }

    public function withTimezone(string $timezone): CityFactoryInterface
    {
        $this->city->setTimezone($timezone);

        return $this;
    }

    public function withRideNamer(string $rideNamerFqcn): CityFactoryInterface
    {
        $this->city->setRideNamer($rideNamerFqcn);

        return $this;
    }

    public function withCreatedAt(\DateTime $createdAt): CityFactoryInterface
    {
        $this->city->setCreatedAt($createdAt);

        return $this;
    }

    public function withRegion(Region $region): CityFactoryInterface
    {
        $this->city->setRegion($region);

        return $this;
    }

    public function withUser(UserInterface $user): CityFactoryInterface
    {
        $this->city->setUser($user);

        return $this;
    }

    public function withName(string $cityName): CityFactoryInterface
    {
        $this->city->setCity($cityName);
        
        return $this;
    }

    public function build(): City
    {
        return $this->city;
    }
}