<?php declare(strict_types=1);

namespace Tests\Util;

use App\Criticalmass\Util\StringUtil;
use PHPUnit\Framework\TestCase;

class StringUtilTest extends TestCase
{
    public function testSimpleCamelCase(): void
    {
        $this->assertEquals('hello_world', StringUtil::camelCaseToUnderscore('helloWorld'));
    }

    public function testUpperCamelCase(): void
    {
        $this->assertEquals('hello_world', StringUtil::camelCaseToUnderscore('HelloWorld'));
    }

    public function testSingleWord(): void
    {
        $this->assertEquals('hello', StringUtil::camelCaseToUnderscore('hello'));
    }

    public function testSingleUppercaseWord(): void
    {
        $this->assertEquals('hello', StringUtil::camelCaseToUnderscore('Hello'));
    }

    public function testMultipleCamelCaseWords(): void
    {
        $this->assertEquals('this_is_a_test', StringUtil::camelCaseToUnderscore('thisIsATest'));
    }

    public function testEmptyString(): void
    {
        $this->assertEquals('', StringUtil::camelCaseToUnderscore(''));
    }

    public function testAlreadyUnderscore(): void
    {
        $this->assertEquals('already_underscore', StringUtil::camelCaseToUnderscore('already_underscore'));
    }

    public function testDoubleUnderscoreAvoidedByDefault(): void
    {
        $this->assertEquals('hello_world', StringUtil::camelCaseToUnderscore('Hello_World'));
    }

    public function testDoubleUnderscoreAllowed(): void
    {
        $this->assertEquals('hello__world', StringUtil::camelCaseToUnderscore('Hello_World', false));
    }

    public function testConsecutiveUppercaseLetters(): void
    {
        $result = StringUtil::camelCaseToUnderscore('parseHTML');
        $this->assertEquals('parse_h_t_m_l', $result);
    }

    public function testAllUppercase(): void
    {
        $result = StringUtil::camelCaseToUnderscore('ABC');
        $this->assertEquals('a_b_c', $result);
    }

    public function testWithNumbers(): void
    {
        $this->assertEquals('test123value', StringUtil::camelCaseToUnderscore('test123value'));
    }

    public function testCamelCaseWithNumbers(): void
    {
        $this->assertEquals('test123_value', StringUtil::camelCaseToUnderscore('test123Value'));
    }
}
