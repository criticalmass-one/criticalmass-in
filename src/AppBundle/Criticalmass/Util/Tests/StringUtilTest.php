<?php declare(strict_types=1);

namespace AppBundle\Criticalmass\Util\Tests;

use AppBundle\Criticalmass\Util\StringUtil;
use PHPUnit\Framework\TestCase;

class StringUtilTest extends TestCase
{
    public function testCamelCaseToUnderscore1(): void
    {
        $this->assertEquals('camel_case', StringUtil::camelCaseToUnderscore('camelCase'));
    }

    public function testCamelCaseToUnderscore2(): void
    {
        $this->assertEquals('camel_case', StringUtil::camelCaseToUnderscore('CamelCase'));
    }

    public function testCamelCaseToUnderscore3(): void
    {
        $this->assertEquals('camel_case', StringUtil::camelCaseToUnderscore('camel_Case'));
    }

    public function testCamelCaseToUnderscore4(): void
    {
        $this->assertEquals('camel_case', StringUtil::camelCaseToUnderscore('Camel_Case'));
    }
}
