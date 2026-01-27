<?php declare(strict_types=1);

namespace Tests\ViewStorage\Persister;

use App\Criticalmass\ViewStorage\Persister\ViewStoragePersister;
use App\Criticalmass\ViewStorage\ViewEntityFactory\ViewEntityFactory;
use App\Criticalmass\ViewStorage\ViewModel\View;
use App\Entity\User;
use App\Serializer\CriticalSerializer;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Doctrine\Persistence\ManagerRegistry;
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
            ->setDateTime(new \Carbon\Carbon());

        $viewEntityFactoryRegistry = $this->createMock(ManagerRegistry::class);
        $viewEntityFactoryRegistry
            ->expects($this->never())
            ->method('getRepository')
            ->with($this->equalTo(User::class));

        $viewStoragePersisterRepository = $this->createMock(ObjectRepository::class);
        $viewStoragePersisterRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->willReturn($testClass);

        $viewStoragePersisterManager = $this->createMock(EntityManagerInterface::class);
        $viewStoragePersisterManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($persistedEntity) use ($expectedPersistedClass) {
                return $persistedEntity instanceof TestView
                    && $persistedEntity->getId() === $expectedPersistedClass->getId()
                    && $persistedEntity->getTest() === $expectedPersistedClass->getTest()
                    && $persistedEntity->getUser() === $expectedPersistedClass->getUser()
                    && $persistedEntity->getDateTime() instanceof \Carbon\Carbon;
            }));

        $viewStoragePersisterRegistry = $this->createMock(ManagerRegistry::class);
        $viewStoragePersisterRegistry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('App\Entity\Test'))
            ->willReturn($viewStoragePersisterRepository);

        $viewStoragePersisterRegistry
            ->expects($this->once())
            ->method('getManager')
            ->willReturn($viewStoragePersisterManager);

        $serializer = new CriticalSerializer();
        $viewEntityFactory = new ViewEntityFactory($viewEntityFactoryRegistry);
        $viewEntityFactory->setEntityNamespace('Tests\\ViewStorage\\');

        $viewStoragePersister = new ViewStoragePersister($viewStoragePersisterRegistry, $serializer, $viewEntityFactory);
        $viewStoragePersister->setEntityNamespace('Tests\\ViewStorage\\');

        $view = new View();
        $view
            ->setDateTime(new \Carbon\Carbon())
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
            ->setDateTime(new \Carbon\Carbon());

        $viewEntityFactoryRepository = $this->createMock(ObjectRepository::class);
        $viewEntityFactoryRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->willReturn($user);

        $viewEntityFactoryRegistry = $this->createMock(ManagerRegistry::class);
        $viewEntityFactoryRegistry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(User::class))
            ->willReturn($viewEntityFactoryRepository);

        $viewStoragePersisterRepository = $this->createMock(ObjectRepository::class);
        $viewStoragePersisterRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->willReturn($testClass);

        $viewStoragePersisterManager = $this->createMock(EntityManagerInterface::class);
        $viewStoragePersisterManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($persistedEntity) use ($expectedPersistedClass) {
                return $persistedEntity instanceof TestView
                    && $persistedEntity->getId() === $expectedPersistedClass->getId()
                    && $persistedEntity->getTest() === $expectedPersistedClass->getTest()
                    && $persistedEntity->getUser() === $expectedPersistedClass->getUser()
                    && $persistedEntity->getDateTime() instanceof \Carbon\Carbon;
            }));

        $viewStoragePersisterRegistry = $this->createMock(ManagerRegistry::class);
        $viewStoragePersisterRegistry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('App\Entity\Test'))
            ->willReturn($viewStoragePersisterRepository);

        $viewStoragePersisterRegistry
            ->expects($this->once())
            ->method('getManager')
            ->willReturn($viewStoragePersisterManager);

        $serializer = new CriticalSerializer();
        $viewEntityFactory = new ViewEntityFactory($viewEntityFactoryRegistry);
        $viewEntityFactory->setEntityNamespace('Tests\\ViewStorage\\');

        $viewStoragePersister = new ViewStoragePersister($viewStoragePersisterRegistry, $serializer, $viewEntityFactory);
        $viewStoragePersister->setEntityNamespace('Tests\\ViewStorage\\');

        $view = new View();
        $view
            ->setDateTime(new \Carbon\Carbon())
            ->setUserId(1)
            ->setEntityClassName('Test')
            ->setEntityId(1);

        $viewStoragePersister->storeView($view);
    }

    public function testViewIncrement(): void
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
            ->setDateTime(new \Carbon\Carbon());

        $viewEntityFactoryRepository = $this->createMock(ObjectRepository::class);
        $viewEntityFactoryRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->willReturn($user);

        $viewEntityFactoryRegistry = $this->createMock(ManagerRegistry::class);
        $viewEntityFactoryRegistry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(User::class))
            ->willReturn($viewEntityFactoryRepository);

        $viewStoragePersisterRepository = $this->createMock(ObjectRepository::class);
        $viewStoragePersisterRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->willReturn($testClass);

        $viewStoragePersisterManager = $this->createMock(EntityManagerInterface::class);
        $viewStoragePersisterManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($persistedEntity) use ($expectedPersistedClass) {
                return $persistedEntity instanceof TestView
                    && $persistedEntity->getId() === $expectedPersistedClass->getId()
                    && $persistedEntity->getTest() === $expectedPersistedClass->getTest()
                    && $persistedEntity->getUser() === $expectedPersistedClass->getUser()
                    && $persistedEntity->getDateTime() instanceof \Carbon\Carbon;
            }));

        $viewStoragePersisterRegistry = $this->createMock(ManagerRegistry::class);
        $viewStoragePersisterRegistry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('App\Entity\Test'))
            ->willReturn($viewStoragePersisterRepository);

        $viewStoragePersisterRegistry
            ->expects($this->once())
            ->method('getManager')
            ->willReturn($viewStoragePersisterManager);

        $serializer = new CriticalSerializer();
        $viewEntityFactory = new ViewEntityFactory($viewEntityFactoryRegistry);
        $viewEntityFactory->setEntityNamespace('Tests\\ViewStorage\\');

        $viewStoragePersister = new ViewStoragePersister($viewStoragePersisterRegistry, $serializer, $viewEntityFactory);
        $viewStoragePersister->setEntityNamespace('Tests\\ViewStorage\\');

        $view = new View();
        $view
            ->setDateTime(new \Carbon\Carbon())
            ->setUserId(1)
            ->setEntityClassName('Test')
            ->setEntityId(1);

        $viewStoragePersister->storeView($view);

        $this->assertEquals(43, $testClass->getViews());
    }
}
