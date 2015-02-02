<?php
/**
 * Created by IntelliJ IDEA.
 * User: malte
 * Date: 02.02.15
 * Time: 15:38
 */

namespace Caldera\CriticalmassCoreBundle\Tests\CitySlugGenerator;

use Caldera\CriticalmassCoreBundle\Utility\CitySlugGenerator\CitySlugGenerator;
use Caldera\CriticalmassCoreBundle\Entity\City;

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