<?php declare(strict_types=1);

namespace Tests\CityCycle\DateTimeValidator;

use App\Criticalmass\Cycles\DateTimeValidator\DateTimeValidator;
use App\Entity\CityCycle;
use PHPUnit\Framework\TestCase;

class DateTimeValidatorTest extends TestCase
{
    public function testValidFromNullValidUntilNull(): void
    {
        $cityCycle = new CityCycle();

        $this->assertTrue(DateTimeValidator::isValidDateTime($cityCycle, new \DateTime('2019-06-30')));
        $this->assertTrue(DateTimeValidator::isValidDateTime($cityCycle, new \DateTime('2019-07-01')));
    }

    public function testValidFromValidUntilNull(): void
    {
        $cityCycle = new CityCycle();
        $cityCycle->setValidFrom(new \DateTime('2019-07-01'));

        $this->assertFalse(DateTimeValidator::isValidDateTime($cityCycle, new \DateTime('2019-06-30')));
        $this->assertTrue(DateTimeValidator::isValidDateTime($cityCycle, new \DateTime('2019-07-01')));
        $this->assertTrue(DateTimeValidator::isValidDateTime($cityCycle, new \DateTime('2019-07-02')));
    }

    public function testValidFromNullValidUntil(): void
    {
        $cityCycle = new CityCycle();
        $cityCycle->setValidUntil(new \DateTime('2019-06-30'));

        $this->assertTrue(DateTimeValidator::isValidDateTime($cityCycle, new \DateTime('2019-06-29')));
        $this->assertTrue(DateTimeValidator::isValidDateTime($cityCycle, new \DateTime('2019-06-30')));
        $this->assertFalse(DateTimeValidator::isValidDateTime($cityCycle, new \DateTime('2019-07-01')));
    }

    public function testValidFromValidUntil(): void
    {
        $cityCycle = new CityCycle();
        $cityCycle
            ->setValidFrom(new \DateTime('2019-06-01'))
            ->setValidUntil(new \DateTime('2019-06-30'));

        $this->assertFalse(DateTimeValidator::isValidDateTime($cityCycle, new \DateTime('2019-05-01')));
        $this->assertFalse(DateTimeValidator::isValidDateTime($cityCycle, new \DateTime('2019-05-31')));
        $this->assertTrue(DateTimeValidator::isValidDateTime($cityCycle, new \DateTime('2019-06-01')));
        $this->assertTrue(DateTimeValidator::isValidDateTime($cityCycle, new \DateTime('2019-06-30')));
        $this->assertFalse(DateTimeValidator::isValidDateTime($cityCycle, new \DateTime('2019-07-01')));
        $this->assertFalse(DateTimeValidator::isValidDateTime($cityCycle, new \DateTime('2019-07-31')));
    }
}