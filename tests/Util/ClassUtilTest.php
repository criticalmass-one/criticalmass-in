<?php declare(strict_types=1);

namespace Tests\Util;

use App\Criticalmass\Util\ClassUtil;
use PHPUnit\Framework\TestCase;

class ClassUtilTest extends TestCase
{
    public function testGetShortnameFromFqcn(): void
    {
        $this->assertEquals('MyClass', ClassUtil::getShortnameFromFqcn('App\\Some\\Namespace\\MyClass'));
    }

    public function testGetShortnameFromFqcnWithSingleSegment(): void
    {
        $this->assertEquals('MyClass', ClassUtil::getShortnameFromFqcn('MyClass'));
    }

    public function testGetShortnameFromFqcnWithDeeplyNested(): void
    {
        $this->assertEquals('DeepClass', ClassUtil::getShortnameFromFqcn('App\\Very\\Deep\\Nested\\Namespace\\DeepClass'));
    }

    public function testGetLowercaseShortnameFromFqcn(): void
    {
        $this->assertEquals('myclass', ClassUtil::getLowercaseShortnameFromFqcn('App\\Some\\Namespace\\MyClass'));
    }

    public function testGetLowercaseShortnameFromFqcnWithUppercase(): void
    {
        $this->assertEquals('uppercase', ClassUtil::getLowercaseShortnameFromFqcn('App\\UPPERCASE'));
    }

    public function testGetShortnameFromObject(): void
    {
        $object = new \stdClass();
        $this->assertEquals('stdClass', ClassUtil::getShortname($object));
    }

    public function testGetShortnameFromDateTimeObject(): void
    {
        $object = new \DateTime();
        $this->assertEquals('DateTime', ClassUtil::getShortname($object));
    }

    public function testGetLowercaseShortnameFromObject(): void
    {
        $object = new \DateTime();
        $this->assertEquals('datetime', ClassUtil::getLowercaseShortname($object));
    }

    public function testGetLcfirstShortnameFromObject(): void
    {
        $object = new \DateTime();
        $this->assertEquals('dateTime', ClassUtil::getLcfirstShortname($object));
    }

    public function testGetLcfirstShortnameFromStdClass(): void
    {
        $object = new \stdClass();
        $this->assertEquals('stdClass', ClassUtil::getLcfirstShortname($object));
    }

    public function testGetShortnameFromClassName(): void
    {
        $this->assertEquals('ClassUtil', ClassUtil::getShortname(ClassUtil::class));
    }

    public function testGetLowercaseShortnameFromClassName(): void
    {
        $this->assertEquals('classutil', ClassUtil::getLowercaseShortname(ClassUtil::class));
    }
}
