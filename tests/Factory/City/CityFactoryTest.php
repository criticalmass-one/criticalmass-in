<?php declare(strict_types=1);

namespace Tests\Factory\City;

use App\Entity\City;
use App\Entity\Region;
use App\Entity\User;
use App\Factory\City\CityFactory;
use Doctrine\Common\Collections\Collection;
use PHPUnit\Framework\TestCase;

class CityFactoryTest extends TestCase
{
    public function testCityFactoryWithNearlyEmptyCity(): void
    {
        $dateTime = new \DateTime();

        $factory = new CityFactory();
        $factory->withCreatedAt($dateTime);

        $actualCity = $factory->build();

        $expectedCity = new City();
        $expectedCity->setCreatedAt($dateTime);

        $this->assertEquals($expectedCity, $actualCity);
    }

    public function testCityCollections(): void
    {
        $factory = new CityFactory();
        $actualCity = $factory->build();

        $this->assertInstanceOf(Collection::class, $actualCity->getRides());
        $this->assertInstanceOf(Collection::class, $actualCity->getSlugs());
        $this->assertInstanceOf(Collection::class, $actualCity->getPosts());
        $this->assertInstanceOf(Collection::class, $actualCity->getPhotos());
        $this->assertInstanceOf(Collection::class, $actualCity->getCycles());
        $this->assertInstanceOf(Collection::class, $actualCity->getSocialNetworkProfiles());
    }

    public function testCityColors(): void
    {
        $dateTime = new \DateTime();

        $red = 55;
        $green = 66;
        $blue = 77;

        $cityFactory = new CityFactory();
        $cityFactory
            ->withColors($red, $green, $blue)
            ->withCreatedAt($dateTime);

        $actualCity = $cityFactory->build();

        $expectedCity = new City();
        $expectedCity
            ->setColorRed($red)
            ->setColorGreen($green)
            ->setColorBlue($blue)
            ->setCreatedAt($dateTime);

        $this->assertEquals($expectedCity, $actualCity);
    }

    /**
     * This may fail sometimes when rand() offers the same colors
     */
    public function testRandomColors(): void
    {
        $dateTime = new \DateTime();

        $cityFactory1 = new CityFactory();
        $cityFactory1
            ->withRandomColors()
            ->withCreatedAt($dateTime);

        $city1 = $cityFactory1->build();

        $cityFactory2 = new CityFactory();
        $cityFactory2
            ->withRandomColors()
            ->withCreatedAt($dateTime);

        $city2 = $cityFactory2->build();

        $this->assertNotEquals($city1, $city2);
        $this->assertNotEquals($city1->getColorRed(), $city2->getColorRed());
        $this->assertNotEquals($city1->getColorGreen(), $city2->getColorGreen());
        $this->assertNotEquals($city1->getColorBlue(), $city2->getColorBlue());
    }

    public function testRegion(): void
    {
        $dateTime = new \DateTime();

        $region = new Region();

        $cityFactory = new CityFactory();
        $cityFactory
            ->withRegion($region)
            ->withCreatedAt($dateTime);

        $actualCity = $cityFactory->build();

        $expectedCity = new City();
        $expectedCity
            ->setRegion($region)
            ->setCreatedAt($dateTime);

        $this->assertEquals($expectedCity, $actualCity);
    }

    public function testUser(): void
    {
        $dateTime = new \DateTime();
        $user = new User();

        $cityFactory = new CityFactory();
        $cityFactory
            ->withUser($user)
            ->withCreatedAt($dateTime);

        $actualCity = $cityFactory->build();

        $expectedCity = new City();
        $expectedCity
            ->setUser($user)
            ->setCreatedAt($dateTime);

        $this->assertEquals($expectedCity, $actualCity);
    }

    public function testCityName(): void
    {
        $dateTime = new \DateTime();

        $cityFactory = new CityFactory();
        $cityFactory
            ->withName('Hamburg')
            ->withCreatedAt($dateTime);

        $actualCity = $cityFactory->build();

        $expectedCity = new City();
        $expectedCity
            ->setCity('Hamburg')
            ->setCreatedAt($dateTime);

        $this->assertEquals($expectedCity, $actualCity);
    }
}