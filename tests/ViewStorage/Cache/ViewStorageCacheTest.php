<?php declare(strict_types=1);

namespace Tests\ViewStorage\Cache;

use App\Criticalmass\ViewStorage\Cache\ViewStorageCache;
use App\Entity\User;
use App\Message\CountViewMessage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Tests\ViewStorage\TestClass;

class ViewStorageCacheTest extends TestCase
{
    public function testWithoutUser(): void
    {
        $token = $this->createMock(UsernamePasswordToken::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn(null);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->atLeastOnce())
            ->method('getToken')
            ->willReturn($token);

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($message) {
                return $message instanceof CountViewMessage
                    && $message->getEntityId() === 1
                    && $message->getEntityClassName() === 'TestClass'
                    && $message->getUserId() === null
                    && $message->getDateTime() instanceof \DateTimeInterface;
            }))
            ->willReturn(new Envelope(new \stdClass()));

        $viewStorageCache = new ViewStorageCache($tokenStorage, $messageBus);

        $testClass = new TestClass();

        $viewStorageCache->countView($testClass);
    }

    public function testWithUser(): void
    {
        $user = new User();
        $user->setId(42);

        $token = $this->createMock(UsernamePasswordToken::class);
        $token
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->atLeastOnce())
            ->method('getToken')
            ->willReturn($token);

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($message) {
                return $message instanceof CountViewMessage
                    && $message->getEntityId() === 1
                    && $message->getEntityClassName() === 'TestClass'
                    && $message->getUserId() === 42
                    && $message->getDateTime() instanceof \DateTimeInterface;
            }))
            ->willReturn(new Envelope(new \stdClass()));

        $viewStorageCache = new ViewStorageCache($tokenStorage, $messageBus);

        $testClass = new TestClass();

        $viewStorageCache->countView($testClass);
    }
}
