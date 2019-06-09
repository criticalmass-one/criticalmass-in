<?php declare(strict_types=1);

namespace Tests\ViewStorage\Cache;

use App\Criticalmass\ViewStorage\Cache\RobustViewStorageCache;
use App\Criticalmass\ViewStorage\Persister\ViewStoragePersister;
use App\Criticalmass\ViewStorage\ViewEntityFactory\ViewEntityFactory;
use App\Entity\User;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerBuilder;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PhpAmqpLib\Exception\AMQPIOException;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\RegistryInterface;
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
            ->with($this->equalTo('App:TestClass'))
            ->will($this->returnValue($viewStoragePersisterRepository));

        $viewStoragePersisterRegistry
            ->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($viewStoragePersisterManager));

        $viewStorageCacheManager = $this->createMock(EntityManagerInterface::class);
        $viewStorageCacheManager
            ->expects($this->once())
            ->method('flush');

        $viewStorageCacheRegistry = $this->createMock(RegistryInterface::class);
        $viewStorageCacheRegistry
            ->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($viewStorageCacheManager));

        $viewEntityFactoryRegistry = $this->createMock(RegistryInterface::class);

        $serializer = SerializerBuilder::create()->build();

        $token = $this->createMock(UsernamePasswordToken::class);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->exactly(2))
            ->method('getToken')
            ->will($this->returnValue($token));

        $producer = $this->createMock(ProducerInterface::class);
        $producer
            ->expects($this->once())
            ->method('publish')
            ->will($this->throwException(new AMQPIOException()));

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
            ->with($this->equalTo('App:TestClass'))
            ->will($this->returnValue($viewStoragePersisterRepository));

        $viewStoragePersisterRegistry
            ->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($viewStoragePersisterManager));

        $viewStorageCacheManager = $this->createMock(EntityManagerInterface::class);
        $viewStorageCacheManager
            ->expects($this->once())
            ->method('flush');

        $viewStorageCacheRegistry = $this->createMock(RegistryInterface::class);
        $viewStorageCacheRegistry
            ->expects($this->once())
            ->method('getManager')
            ->will($this->returnValue($viewStorageCacheManager));

        $viewEntityFactoryRepository = $this->createMock(ObjectRepository::class);
        $viewEntityFactoryRepository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(42))
            ->will($this->returnValue($user));

        $viewEntityFactoryRegistry = $this->createMock(RegistryInterface::class);
        $viewEntityFactoryRegistry
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(User::class))
            ->will($this->returnValue($viewEntityFactoryRepository));

        $serializer = SerializerBuilder::create()->build();

        $token = $this->createMock(UsernamePasswordToken::class);
        $token
            ->expects($this->exactly(2))
            ->method('getUser')
            ->will($this->returnValue($user));

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->exactly(2))
            ->method('getToken')
            ->will($this->returnValue($token));

        $producer = $this->createMock(ProducerInterface::class);
        $producer
            ->expects($this->once())
            ->method('publish')
            ->will($this->throwException(new AMQPIOException()));

        $viewEntityFactory = new ViewEntityFactory($viewEntityFactoryRegistry);
        $viewEntityFactory->setEntityNamespace('Tests\\ViewStorage\\');

        $viewStoragePersister = new ViewStoragePersister($viewStoragePersisterRegistry, $serializer, $viewEntityFactory);
        $viewStoragePersister->setEntityNamespace('Tests\\ViewStorage\\');

        $robustViewStorageCache = new RobustViewStorageCache($viewStorageCacheRegistry, $viewStoragePersister, $tokenStorage, $producer, $serializer);

        $robustViewStorageCache->countView($testClass);
    }
}
