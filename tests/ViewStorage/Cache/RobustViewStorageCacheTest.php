<?php declare(strict_types=1);

namespace Tests\ViewStorage\Cache;

use App\Criticalmass\ViewStorage\Cache\RobustViewStorageCache;
use App\Criticalmass\ViewStorage\Persister\ViewStoragePersisterInterface;
use App\Criticalmass\ViewStorage\ViewModel\View;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Tests\ViewStorage\TestClass;

class RobustViewStorageCacheTest extends TestCase
{
    public function testFallbackWithoutUser(): void
    {
        $testClass = new TestClass();

        $token = $this->createMock(UsernamePasswordToken::class);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->exactly(2))
            ->method('getToken')
            ->will($this->returnValue($token));

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->will($this->throwException(new TransportException('Connection refused')));

        $viewStoragePersister = $this->createMock(ViewStoragePersisterInterface::class);
        $viewStoragePersister
            ->expects($this->once())
            ->method('storeView')
            ->with($this->callback(function (View $view) {
                return $view->getEntityId() === 1
                    && $view->getEntityClassName() === 'TestClass'
                    && $view->getUserId() === null;
            }));

        $robustViewStorageCache = new RobustViewStorageCache(
            $viewStoragePersister,
            $tokenStorage,
            $messageBus
        );

        $robustViewStorageCache->countView($testClass);
    }

    public function testFallbackWithUser(): void
    {
        $testClass = new TestClass();

        $user = new User();
        $user->setId(42);

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

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->will($this->throwException(new TransportException('Connection refused')));

        $viewStoragePersister = $this->createMock(ViewStoragePersisterInterface::class);
        $viewStoragePersister
            ->expects($this->once())
            ->method('storeView')
            ->with($this->callback(function (View $view) {
                return $view->getEntityId() === 1
                    && $view->getEntityClassName() === 'TestClass'
                    && $view->getUserId() === 42;
            }));

        $robustViewStorageCache = new RobustViewStorageCache(
            $viewStoragePersister,
            $tokenStorage,
            $messageBus
        );

        $robustViewStorageCache->countView($testClass);
    }

    public function testSuccessfulDispatch(): void
    {
        $testClass = new TestClass();

        $token = $this->createMock(UsernamePasswordToken::class);

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($token));

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->willReturn(new Envelope(new \stdClass()));

        $viewStoragePersister = $this->createMock(ViewStoragePersisterInterface::class);
        $viewStoragePersister
            ->expects($this->never())
            ->method('storeView');

        $robustViewStorageCache = new RobustViewStorageCache(
            $viewStoragePersister,
            $tokenStorage,
            $messageBus
        );

        $robustViewStorageCache->countView($testClass);
    }
}
