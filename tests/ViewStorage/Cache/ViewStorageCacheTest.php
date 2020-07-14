<?php declare(strict_types=1);

namespace Tests\ViewStorage\Cache;

use App\Criticalmass\ViewStorage\Cache\ViewStorageCache;
use App\Entity\User;
use JMS\Serializer\SerializerBuilder;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Tests\ViewStorage\TestClass;

class ViewStorageCacheTest extends TestCase
{
    public function testWithoutUser(): void
    {
        $expectedJson = sprintf(
            '{"entity_id":1,"entity_class_name":"TestClass","date_time":%d}',
            (new \DateTime())->format('U')
        );

        $token = $this->createMock(UsernamePasswordToken::class);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($token));

        $producer = $this->createMock(ProducerInterface::class);
        $producer
            ->expects($this->once())
            ->method('publish')
            ->with($this->equalTo($expectedJson));

        $serializer = SerializerBuilder::create()->build();

        $viewStorageCache = new ViewStorageCache($tokenStorage, $producer, $serializer);

        $testClass = new TestClass();

        $viewStorageCache->countView($testClass);
    }

    public function testWithUser(): void
    {
        $expectedJson = sprintf(
            '{"entity_id":1,"entity_class_name":"TestClass","user_id":42,"date_time":%d}',
            (new \DateTime())->format('U')
        );

        $user = new User();
        $user->setId(42);

        $token = $this->createMock(UsernamePasswordToken::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->will($this->returnValue($user));

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($token));

        $producer = $this->createMock(ProducerInterface::class);
        $producer
            ->expects($this->once())
            ->method('publish')
            ->with($this->equalTo($expectedJson));

        $serializer = SerializerBuilder::create()->build();

        $viewStorageCache = new ViewStorageCache($tokenStorage, $producer, $serializer);

        $testClass = new TestClass;

        $viewStorageCache->countView($testClass);
    }
}