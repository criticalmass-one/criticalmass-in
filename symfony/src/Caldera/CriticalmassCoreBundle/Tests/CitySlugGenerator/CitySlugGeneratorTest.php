<?php

namespace Caldera\CriticalmassCoreBundle\Tests\CitySlugGenerator;

use Caldera\CriticalmassCoreBundle\Utility\CitySlugGenerator\CitySlugGenerator;
use Caldera\CriticalmassCoreBundle\Entity\City;
use PHPUnit_Framework_TestCase;

class CalculatorTest extends PHPUnit_Framework_TestCase
{
    public function test1()
    {
        $city = new City();
        $city->setCity('Hamburg');

        $csg = new CitySlugGenerator($city);

        $slug = $csg->execute();

        $this->assertEquals($slug, "hamburg");
    }

    public function test2()
    {
        $city = new City();
        $city->setCity('Hamburg-Altona');

        $csg = new CitySlugGenerator($city);

        $slug = $csg->execute();

        $this->assertEquals($slug, "hamburg-altona");
    }

    public function test3()
    {
        $city = new City();
        $city->setCity('München');

        $csg = new CitySlugGenerator($city);

        $slug = $csg->execute();

        $this->assertEquals($slug, "muenchen");
    }
}