<?php declare(strict_types=1);

namespace Tests\ViewStorage\Cache;

use App\Criticalmass\ViewStorage\Cache\ViewStorageCache;
use App\Criticalmass\ViewStorage\ViewModel\View;
use App\Entity\User;
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

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($token));

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (View $view) {
                return $view->getEntityId() === 1
                    && $view->getEntityClassName() === 'TestClass'
                    && $view->getUserId() === null;
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
            ->will($this->returnValue($user));

        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage
            ->expects($this->once())
            ->method('getToken')
            ->will($this->returnValue($token));

        $messageBus = $this->createMock(MessageBusInterface::class);
        $messageBus
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function (View $view) {
                return $view->getEntityId() === 1
                    && $view->getEntityClassName() === 'TestClass'
                    && $view->getUserId() === 42;
            }))
            ->willReturn(new Envelope(new \stdClass()));

        $viewStorageCache = new ViewStorageCache($tokenStorage, $messageBus);

        $testClass = new TestClass;

        $viewStorageCache->countView($testClass);
    }
}
