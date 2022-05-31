<?php declare(strict_types=1);

namespace Tests\CitySlug\Generator;

use App\Criticalmass\CitySlug\Generator\UmlautsCitySlugGenerator;
use App\Entity\City;
use App\Entity\CitySlug;
use PHPUnit\Framework\TestCase;

class UmlautsCitySlugGeneratorTest extends TestCase
{
    public function testHamburg(): void
    {
        $generator = new UmlautsCitySlugGenerator();

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
        $generator = new UmlautsCitySlugGenerator();

        $city = new City();
        $city->setCity('Köln');

        $actualCitySlug = $generator->generate($city);

        $expectedCitySlug = new CitySlug();
        $expectedCitySlug->setCity($city)
            ->setSlug('koeln');

        $this->assertEquals($expectedCitySlug, $actualCitySlug);
    }

    public function testDuesseldorf(): void
    {
        $generator = new UmlautsCitySlugGenerator();

        $city = new City();
        $city->setCity('Düsseldorf');

        $actualCitySlug = $generator->generate($city);

        $expectedCitySlug = new CitySlug();
        $expectedCitySlug->setCity($city)
            ->setSlug('duesseldorf');

        $this->assertEquals($expectedCitySlug, $actualCitySlug);
    }

    public function testAUmlautCity(): void
    {
        $generator = new UmlautsCitySlugGenerator();

        $city = new City();
        $city->setCity('Ästadt');

        $actualCitySlug = $generator->generate($city);

        $expectedCitySlug = new CitySlug();
        $expectedCitySlug->setCity($city)
            ->setSlug('aestadt');

        $this->assertEquals($expectedCitySlug, $actualCitySlug);
    }

    public function testFassbach(): void
    {
        $generator = new UmlautsCitySlugGenerator();

        $city = new City();
        $city->setCity('Faßbach');

        $actualCitySlug = $generator->generate($city);

        $expectedCitySlug = new CitySlug();
        $expectedCitySlug->setCity($city)
            ->setSlug('fassbach');

        $this->assertEquals($expectedCitySlug, $actualCitySlug);
    }
}