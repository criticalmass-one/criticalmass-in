<?php declare(strict_types=1);

namespace Tests\ViewStorage\ViewEntityFactory;

use App\Criticalmass\ViewStorage\ViewEntityFactory\ViewEntityFactory;
use App\Criticalmass\ViewStorage\ViewModel\View;
use App\Entity\User;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\TestCase;
use Doctrine\Persistence\ManagerRegistry;
use Tests\ViewStorage\TestClass;
use Tests\ViewStorage\TestClassView;

class ViewEntityFactoryTest extends TestCase
{
    public function testFactoryWithoutUser(): void
    {
        $registry = $this->createMock(ManagerRegistry::class);

        $dateTime = new \Carbon\Carbon();
        $testClass = new TestClass();
        $viewEntityFactory = new ViewEntityFactory($registry);
        $viewEntityFactory->setEntityNamespace('Tests\\ViewStorage\\');

        $viewModel = new View();
        $viewModel
            ->setDateTime($dateTime)
            ->setEntityId(1)
            ->setEntityClassName('TestClass');

        $actualViewEntity = $viewEntityFactory->createViewEntity($viewModel, $testClass);

        $expectedViewEntity = new TestClassView();
        $expectedViewEntity
            ->setId(1)
            ->setTestClass($testClass)
            ->setDateTime($dateTime);

        $this->assertEquals($expectedViewEntity, $actualViewEntity);
    }

    public function testFactoryWithUser(): void
    {
        $user = new User();
        $user->setId(1);

        $repository = $this->createMock(ObjectRepository::class);
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->will($this->returnValue($user));

        $registry = $this->createMock(ManagerRegistry::class);
        $registry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(User::class))
            ->will($this->returnValue($repository));

        $dateTime = new \Carbon\Carbon();
        $testClass = new TestClass();
        $viewEntityFactory = new ViewEntityFactory($registry);
        $viewEntityFactory->setEntityNamespace('Tests\\ViewStorage\\');

        $viewModel = new View();
        $viewModel
            ->setDateTime($dateTime)
            ->setEntityId(1)
            ->setUserId(1)
            ->setEntityClassName('TestClass');

        $actualViewEntity = $viewEntityFactory->createViewEntity($viewModel, $testClass);

        $expectedViewEntity = new TestClassView();
        $expectedViewEntity
            ->setId(1)
            ->setTestClass($testClass)
            ->setDateTime($dateTime)
            ->setUser($user);

        $this->assertEquals($expectedViewEntity, $actualViewEntity);
    }
}
