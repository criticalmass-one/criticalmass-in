<?php declare(strict_types=1);

namespace Tests\ViewStorage\Persister;

use App\Criticalmass\ViewStorage\Persister\ViewStoragePersister;
use App\Criticalmass\ViewStorage\ViewEntityFactory\ViewEntityFactory;
use App\Criticalmass\ViewStorage\ViewModel\View;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Tests\ViewStorage\TestClass;
use Tests\ViewStorage\TestView;

class ViewStoragePersisterTest extends TestCase
{
    public function testWithoutUser(): void
    {
        $testClass = new TestClass();

        $expectedPersistedClass = new TestView();
        $expectedPersistedClass
            ->setId(1)
            ->setTest($testClass)
            ->setDateTime(new \DateTime());

        $viewEntityFactoryRegistry = $this->createMock(RegistryInterface::class);
        $viewEntityFactoryRegistry
            ->expects($this->never())
            ->method('getRepository')
            ->with($this->equalTo(User::class));

        $viewStoragePersisterRepository = $this->createMock(ObjectRepository::class);
        $viewStoragePersisterRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->will($this->returnValue($testClass));

        $viewStoragePersisterManager = $this->createMock(EntityManagerInterface::class);
        $viewStoragePersisterManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($expectedPersistedClass, 0.5));

        $viewStoragePersisterRegistry = $this->createMock(RegistryInterface::class);
        $viewStoragePersisterRegistry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('App:Test'))
            ->will($this->returnValue($viewStoragePersisterRepository));

        $viewStoragePersisterRegistry
            ->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($viewStoragePersisterManager));

        $serializer = SerializerBuilder::create()->build();
        $viewEntityFactory = new ViewEntityFactory($viewEntityFactoryRegistry);
        $viewEntityFactory->setEntityNamespace('Tests\\ViewStorage\\');

        $viewStoragePersister = new ViewStoragePersister($viewStoragePersisterRegistry, $serializer, $viewEntityFactory);
        $viewStoragePersister->setEntityNamespace('Tests\\ViewStorage\\');

        $view = new View();
        $view
            ->setDateTime(new \DateTime())
            ->setEntityClassName('Test')
            ->setEntityId(1);

        $viewStoragePersister->storeView($view);
    }

    public function testWithUser(): void
    {
        $user = new User();
        $user->setId(1);

        $testClass = new TestClass();

        $expectedPersistedClass = new TestView();
        $expectedPersistedClass
            ->setId(1)
            ->setTest($testClass)
            ->setUser($user)
            ->setDateTime(new \DateTime());

        $viewEntityFactoryRepository = $this->createMock(ObjectRepository::class);
        $viewEntityFactoryRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->will($this->returnValue($user));

        $viewEntityFactoryRegistry = $this->createMock(RegistryInterface::class);
        $viewEntityFactoryRegistry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(User::class))
            ->will($this->returnValue($viewEntityFactoryRepository));

        $viewStoragePersisterRepository = $this->createMock(ObjectRepository::class);
        $viewStoragePersisterRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->will($this->returnValue($testClass));

        $viewStoragePersisterManager = $this->createMock(EntityManagerInterface::class);
        $viewStoragePersisterManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($expectedPersistedClass, 0.5));

        $viewStoragePersisterRegistry = $this->createMock(RegistryInterface::class);
        $viewStoragePersisterRegistry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('App:Test'))
            ->will($this->returnValue($viewStoragePersisterRepository));

        $viewStoragePersisterRegistry
            ->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($viewStoragePersisterManager));

        $serializer = SerializerBuilder::create()->build();
        $viewEntityFactory = new ViewEntityFactory($viewEntityFactoryRegistry);
        $viewEntityFactory->setEntityNamespace('Tests\\ViewStorage\\');

        $viewStoragePersister = new ViewStoragePersister($viewStoragePersisterRegistry, $serializer, $viewEntityFactory);
        $viewStoragePersister->setEntityNamespace('Tests\\ViewStorage\\');

        $view = new View();
        $view
            ->setDateTime(new \DateTime())
            ->setUserId(1)
            ->setEntityClassName('Test')
            ->setEntityId(1);

        $viewStoragePersister->storeView($view);
    }

    public function testViewIncement(): void
    {
        $user = new User();
        $user->setId(1);

        $testClass = new TestClass();
        $testClass->setViews(42);

        $expectedPersistedClass = new TestView();
        $expectedPersistedClass
            ->setId(1)
            ->setTest($testClass)
            ->setUser($user)
            ->setDateTime(new \DateTime());

        $viewEntityFactoryRepository = $this->createMock(ObjectRepository::class);
        $viewEntityFactoryRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->will($this->returnValue($user));

        $viewEntityFactoryRegistry = $this->createMock(RegistryInterface::class);
        $viewEntityFactoryRegistry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(User::class))
            ->will($this->returnValue($viewEntityFactoryRepository));

        $viewStoragePersisterRepository = $this->createMock(ObjectRepository::class);
        $viewStoragePersisterRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->will($this->returnValue($testClass));

        $viewStoragePersisterManager = $this->createMock(EntityManagerInterface::class);
        $viewStoragePersisterManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->equalTo($expectedPersistedClass, 0.5));

        $viewStoragePersisterRegistry = $this->createMock(RegistryInterface::class);
        $viewStoragePersisterRegistry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('App:Test'))
            ->will($this->returnValue($viewStoragePersisterRepository));

        $viewStoragePersisterRegistry
            ->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($viewStoragePersisterManager));

        $serializer = SerializerBuilder::create()->build();
        $viewEntityFactory = new ViewEntityFactory($viewEntityFactoryRegistry);
        $viewEntityFactory->setEntityNamespace('Tests\\ViewStorage\\');

        $viewStoragePersister = new ViewStoragePersister($viewStoragePersisterRegistry, $serializer, $viewEntityFactory);
        $viewStoragePersister->setEntityNamespace('Tests\\ViewStorage\\');

        $view = new View();
        $view
            ->setDateTime(new \DateTime())
            ->setUserId(1)
            ->setEntityClassName('Test')
            ->setEntityId(1);

        $viewStoragePersister->storeView($view);

        $this->assertEquals(43, $testClass->getViews());
    }
}