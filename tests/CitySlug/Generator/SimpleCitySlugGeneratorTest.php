<?php declare(strict_types=1);

namespace Tests\CitySlug\Generator;

use App\Criticalmass\CitySlug\Generator\SimpleCitySlugGenerator;
use App\Entity\City;
use App\Entity\CitySlug;
use PHPUnit\Framework\TestCase;

class SimpleCitySlugGeneratorTest extends TestCase
{
    public function testHamburg(): void
    {
        $generator = new SimpleCitySlugGenerator();

        $city = new City();
        $city->setCity('Hamburg');

        $actualCitySlug = $generator->generate($city);

        $expectedCitySlug = new CitySlug();
        $expectedCitySlug->setCity($city)
            ->setSlug('hamburg');

        $this->assertEquals($expectedCitySlug, $actualCitySlug);
    }

    public function testKoeln(): void
    {
        $generator = new SimpleCitySlugGenerator();

        $city = new City();
        $city->setCity('Köln');

        $actualCitySlug = $generator->generate($city);

        $expectedCitySlug = new CitySlug();
        $expectedCitySlug->setCity($city)
            ->setSlug('koln');

        $this->assertEquals($expectedCitySlug, $actualCitySlug);
    }

    public function testDuesseldorf(): void
    {
        $generator = new SimpleCitySlugGenerator();

        $city = new City();
        $city->setCity('Düsseldorf');

        $actualCitySlug = $generator->generate($city);

        $expectedCitySlug = new CitySlug();
        $expectedCitySlug->setCity($city)
            ->setSlug('dusseldorf');

        $this->assertEquals($expectedCitySlug, $actualCitySlug);
    }

    public function testAUmlautCity(): void
    {
        $generator = new SimpleCitySlugGenerator();

        $city = new City();
        $city->setCity('Ästadt');

        $actualCitySlug = $generator->generate($city);

        $expectedCitySlug = new CitySlug();
        $expectedCitySlug->setCity($city)
            ->setSlug('astadt');

        $this->assertEquals($expectedCitySlug, $actualCitySlug);
    }

    public function testFassbach(): void
    {
        $generator = new SimpleCitySlugGenerator();

        $city = new City();
        $city->setCity('Faßbach');

        $actualCitySlug = $generator->generate($city);

        $expectedCitySlug = new CitySlug();
        $expectedCitySlug->setCity($city)
            ->setSlug('fa-bach');

        $this->assertEquals($expectedCitySlug, $actualCitySlug);
    }
}