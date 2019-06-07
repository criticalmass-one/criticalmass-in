<?php declare(strict_types=1);

namespace Tests\CitySlug\Handler;

use App\Criticalmass\CitySlug\Handler\CitySlugHandler;
use App\Entity\City;
use App\Entity\CitySlug;
use PHPUnit\Framework\TestCase;

class CitySlugHandlerTest extends TestCase
{
    public function testHamburg(): void
    {
        $city = new City();
        $city->setCity('Hamburg');

        $actualCitySlugs = CitySlugHandler::createSlugsForCity($city);

        $expectedCitySlugs = [
            (new CitySlug('hamburg'))->setCity($city),
        ];

        $this->assertEquals($expectedCitySlugs, $actualCitySlugs);
        $this->assertCount(1, $actualCitySlugs);
    }

    public function testKoeln(): void
    {
        $city = new City();
        $city->setCity('Köln');

        $actualCitySlugs = CitySlugHandler::createSlugsForCity($city);

        $expectedCitySlugs = [
            (new CitySlug('koln'))->setCity($city),
            (new CitySlug('koeln'))->setCity($city),
        ];

        $this->assertEquals($expectedCitySlugs, $actualCitySlugs);
        $this->assertCount(2, $actualCitySlugs);
    }
}
