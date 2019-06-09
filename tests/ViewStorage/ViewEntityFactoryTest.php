<?php declare(strict_types=1);

namespace Tests\ViewStorage;

use App\Criticalmass\ViewStorage\ViewEntityFactory\ViewEntityFactory;
use App\Criticalmass\ViewStorage\ViewModel\View;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ViewEntityFactoryTest extends TestCase
{
    public function testFactoryWithoutUser(): void
    {
        $registry = $this->createMock(RegistryInterface::class);

        $dateTime = new \DateTime();
        $testClass = new TestClass();
        $viewEntityFactory = new ViewEntityFactory($registry);

        $viewModel = new View();
        $viewModel
            ->setDateTime($dateTime)
            ->setEntityId(1)
            ->setEntityClassName('TestClass');

        $actualViewEntity = $viewEntityFactory->createViewEntity($viewModel, $testClass, null, 'Tests\\ViewStorage\\');

        $expectedViewEntity = new TestClassView();
        $expectedViewEntity
            ->setId(1)
            ->setTestClass($testClass)
            ->setDateTime($dateTime);

        $this->assertEquals($expectedViewEntity, $actualViewEntity);
    }

    public function testFactoryWithUser(): void
    {
        $registry = $this->createMock(RegistryInterface::class);

        $dateTime = new \DateTime();
        $testClass = new TestClass();
        $viewEntityFactory = new ViewEntityFactory($registry);

        $viewModel = new View();
        $viewModel
            ->setDateTime($dateTime)
            ->setEntityId(1)
            ->setUserId(1)
            ->setEntityClassName('TestClass');

        $actualViewEntity = $viewEntityFactory->createViewEntity($viewModel, $testClass, null, 'Tests\\ViewStorage\\');

        $expectedViewEntity = new TestClassView();
        $expectedViewEntity
            ->setId(1)
            ->setTestClass($testClass)
            ->setDateTime($dateTime);

        $this->assertEquals($expectedViewEntity, $actualViewEntity);
    }
}
