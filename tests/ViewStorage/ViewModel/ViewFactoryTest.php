<?php declare(strict_types=1);

namespace Tests\ViewStorage\ViewModel;

use App\Criticalmass\ViewStorage\ViewModel\View;
use App\Criticalmass\ViewStorage\ViewModel\ViewFactory;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Tests\ViewStorage\TestClass;

class ViewFactoryTest extends TestCase
{
    public function testFactory(): void
    {
        $testClass = new TestClass();
        $userMock = new User();
        $dateTime = new \DateTime();

        $expectedView = new View();
        $expectedView
            ->setUserId(null)
            ->setEntityClassName('TestClass')
            ->setEntityId(1)
            ->setDateTime($dateTime);

        $this->assertEquals($expectedView, ViewFactory::createView($testClass, $userMock, $dateTime));
    }

    public function testWithUser(): void
    {
        $testClass = new TestClass();
        $userMock = new User();
        $userMock->setId(42);

        $dateTime = new \DateTime();

        $expectedView = new View();
        $expectedView
            ->setUserId(42)
            ->setEntityClassName('TestClass')
            ->setEntityId(1)
            ->setDateTime($dateTime);

        $this->assertEquals($expectedView, ViewFactory::createView($testClass, $userMock, $dateTime));
    }

    public function testWithAnonUser(): void
    {
        $testClass = new TestClass();
        $userMock = 'anon';

        $dateTime = new \DateTime();

        $expectedView = new View();
        $expectedView
            ->setUserId(null)
            ->setEntityClassName('TestClass')
            ->setEntityId(1)
            ->setDateTime($dateTime);

        $this->assertEquals($expectedView, ViewFactory::createView($testClass, $userMock, $dateTime));
    }
}
