<?php declare(strict_types=1);

namespace Tests\ViewStorage\Cache;

use App\Criticalmass\ViewStorage\Cache\RobustViewStorageCache;
use App\Criticalmass\ViewStorage\Persister\ViewStoragePersister;
use App\Criticalmass\ViewStorage\ViewEntityFactory\ViewEntityFactory;
use App\Entity\User;
use Doctrine\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerBuilder;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PhpAmqpLib\Exception\AMQPIOException;
use PHPUnit\Framework\TestCase;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Tests\ViewStorage\TestClass;
use Tests\ViewStorage\TestClassView;

class RobustViewStorageCacheTest extends TestCase
{
    public function testWithoutUser(): void
    {
        $testClass = new TestClass;

        $expectedPersistedClass = new TestClassView();
        $expectedPersistedClass
            ->setId(1)
            ->setDateTime(new \DateTime())
            ->setTestClass($testClass);

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
                return $persistedEntity instanceof TestClassView
                    && $persistedEntity->getId() === $expectedPersistedClass->getId()
                    && $persistedEntity->getTestClass() === $expectedPersistedClass->getTestClass()
                    && $persistedEntity->getUser() === $expectedPersistedClass->getUser()
                    && $persistedEntity->getDateTime() instanceof \DateTime;
            }));

        $viewStoragePersisterRegistry = $this->createMock(ManagerRegistry::class);
        $viewStoragePersisterRegistry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('App\Entity\TestClass'))
            ->willReturn($viewStoragePersisterRepository);

        $viewStoragePersisterRegistry
            ->expects($this->once())
            ->method('getManager')
            ->willReturn($viewStoragePersisterManager);

        $viewStorageCacheManager = $this->createMock(EntityManagerInterface::class);
        $viewStorageCacheManager
            ->expects($this->never())
            ->method('flush');

        $viewStorageCacheRegistry = $this->createMock(ManagerRegistry::class);
        $viewStorageCacheRegistry
            ->method('getManager')
            ->willReturn($viewStorageCacheManager);

        $viewEntityFactoryRegistry = $this->createMock(ManagerRegistry::class);

        $serializer = SerializerBuilder::create()->build();

        $token = $this->createMock(UsernamePasswordToken::class);
        $token
            ->method('getUser')
            ->willReturn(null);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->exactly(4))
            ->method('getToken')
            ->willReturn($token);

        $producer = $this->createMock(ProducerInterface::class);
        $producer
            ->expects($this->once())
            ->method('publish')
            ->willThrowException(new AMQPIOException());

        $viewEntityFactory = new ViewEntityFactory($viewEntityFactoryRegistry);
        $viewEntityFactory->setEntityNamespace('Tests\\ViewStorage\\');

        $viewStoragePersister = new ViewStoragePersister($viewStoragePersisterRegistry, $serializer, $viewEntityFactory);
        $viewStoragePersister->setEntityNamespace('Tests\\ViewStorage\\');

        $robustViewStorageCache = new RobustViewStorageCache($viewStorageCacheRegistry, $viewStoragePersister, $tokenStorage, $producer, $serializer);

        $robustViewStorageCache->countView($testClass);
    }

    public function testWithUser(): void
    {
        $testClass = new TestClass;

        $user = new User();
        $user->setId(42);

        $expectedPersistedClass = new TestClassView();
        $expectedPersistedClass
            ->setId(1)
            ->setUser($user)
            ->setDateTime(new \DateTime())
            ->setTestClass($testClass);

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
                return $persistedEntity instanceof TestClassView
                    && $persistedEntity->getId() === $expectedPersistedClass->getId()
                    && $persistedEntity->getTestClass() === $expectedPersistedClass->getTestClass()
                    && $persistedEntity->getUser() === $expectedPersistedClass->getUser()
                    && $persistedEntity->getDateTime() instanceof \DateTime;
            }));

        $viewStoragePersisterRegistry = $this->createMock(ManagerRegistry::class);
        $viewStoragePersisterRegistry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo('App\Entity\TestClass'))
            ->willReturn($viewStoragePersisterRepository);

        $viewStoragePersisterRegistry
            ->expects($this->once())
            ->method('getManager')
            ->willReturn($viewStoragePersisterManager);

        $viewStorageCacheManager = $this->createMock(EntityManagerInterface::class);
        $viewStorageCacheManager
            ->expects($this->never())
            ->method('flush');

        $viewStorageCacheRegistry = $this->createMock(ManagerRegistry::class);
        $viewStorageCacheRegistry
            ->method('getManager')
            ->willReturn($viewStorageCacheManager);

        $viewEntityFactoryRepository = $this->createMock(ObjectRepository::class);
        $viewEntityFactoryRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(42))
            ->willReturn($user);

        $viewEntityFactoryRegistry = $this->createMock(ManagerRegistry::class);
        $viewEntityFactoryRegistry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(User::class))
            ->willReturn($viewEntityFactoryRepository);

        $serializer = SerializerBuilder::create()->build();

        $token = $this->createMock(UsernamePasswordToken::class);
        $token
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->exactly(4))
            ->method('getToken')
            ->willReturn($token);

        $producer = $this->createMock(ProducerInterface::class);
        $producer
            ->expects($this->once())
            ->method('publish')
            ->willThrowException(new AMQPIOException());

        $viewEntityFactory = new ViewEntityFactory($viewEntityFactoryRegistry);
        $viewEntityFactory->setEntityNamespace('Tests\\ViewStorage\\');

        $viewStoragePersister = new ViewStoragePersister($viewStoragePersisterRegistry, $serializer, $viewEntityFactory);
        $viewStoragePersister->setEntityNamespace('Tests\\ViewStorage\\');

        $robustViewStorageCache = new RobustViewStorageCache($viewStorageCacheRegistry, $viewStoragePersister, $tokenStorage, $producer, $serializer);

        $robustViewStorageCache->countView($testClass);
    }
}
