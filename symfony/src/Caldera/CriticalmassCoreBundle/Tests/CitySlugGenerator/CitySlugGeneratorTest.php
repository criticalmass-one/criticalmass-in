<?php

namespace Caldera\CriticalmassCoreBundle\Tests\CitySlugGenerator;

use Caldera\CriticalmassCoreBundle\Utility\CitySlugGenerator\CitySlugGenerator;
use Caldera\CriticalmassCoreBundle\Entity\City;
use PHPUnit_Framework_TestCase;

class CalculatorTest extends PHPUnit_Framework_TestCase
{
    public function testAdd()
    {
        $city = new City();
        
        $csg = new CitySlugGenerator($city);
        
        $slug = $csg->execute();

        $this->assertEquals($slug, "foo");
    }
}