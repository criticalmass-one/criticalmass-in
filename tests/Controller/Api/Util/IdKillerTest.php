<?php declare(strict_types=1);

namespace Tests\Controller\Api\Util;

use PHPUnit\Framework\TestCase;

class IdKillerTest extends TestCase
{
    public function testIdKiller(): void
    {
        $testJsonString = '{"slug":"hamburg","id":7,"mainSlug":{"id":7,"slug":"hamburg"},"name":"Hamburg","title":"Critical Mass Hamburg","description":null,"url":null,"facebook":null,"twitter":null,"latitude":0,"longitude":0,"slugs":[{"id":7,"slug":"hamburg"}],"cityPopulation":0,"punchLine":null,"longDescription":null,"timezone":"Europe\/Berlin","threadNumber":0,"postNumber":0,"colorRed":0,"colorGreen":0,"colorBlue":0}';
        $expectedJsonString = '{"slug":"hamburg","mainSlug":{"slug":"hamburg"},"name":"Hamburg","title":"Critical Mass Hamburg","description":null,"url":null,"facebook":null,"twitter":null,"latitude":0,"longitude":0,"slugs":[{"slug":"hamburg"}],"cityPopulation":0,"punchLine":null,"longDescription":null,"timezone":"Europe\/Berlin","threadNumber":0,"postNumber":0,"colorRed":0,"colorGreen":0,"colorBlue":0}';

        $actualJsonString = IdKiller::removeIds($testJsonString);

        $this->assertEquals($expectedJsonString, $actualJsonString);
    }
}