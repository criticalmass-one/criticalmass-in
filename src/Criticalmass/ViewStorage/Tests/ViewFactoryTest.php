<?php declare(strict_types=1);

namespace Tests\RideGenerator;

use App\Criticalmass\ViewStorage\Tests\TestClass;
use App\Criticalmass\ViewStorage\ViewModel\View;
use App\Criticalmass\ViewStorage\ViewModel\ViewFactory;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ViewFactoryTest extends TestCase
{
    public function test1(): void
    {
        $testClass = new TestClass();
        $userMock = new User();
        $dateTime = new \DateTime();

        $expectedView = new View();
        $expectedView->setUserId(null)
            ->setEntityClassName('TestClass')
            ->setEntityId(1)
            ->setDateTime($dateTime);

        $this->assertEquals($expectedView, ViewFactory::createView($testClass, $userMock, $dateTime));
    }
}
