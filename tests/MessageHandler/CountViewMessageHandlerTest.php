<?php declare(strict_types=1);

namespace Tests\MessageHandler;

use App\Criticalmass\ViewStorage\Persister\ViewStoragePersisterInterface;
use App\Criticalmass\ViewStorage\ViewModel\View;
use App\Message\CountViewMessage;
use App\MessageHandler\CountViewMessageHandler;
use PHPUnit\Framework\TestCase;

class CountViewMessageHandlerTest extends TestCase
{
    public function testHandleMessageWithoutUser(): void
    {
        $dateTime = new \Carbon\Carbon('2024-01-15 14:30:00');

        $message = new CountViewMessage(
            entityId: 42,
            entityClassName: 'Photo',
            userId: null,
            dateTime: $dateTime
        );

        $viewStoragePersister = $this->createMock(ViewStoragePersisterInterface::class);
        $viewStoragePersister
            ->expects($this->once())
            ->method('storeView')
            ->with(
                $this->callback(function (View $view) use ($dateTime) {
                    return $view->getEntityId() === 42
                        && $view->getEntityClassName() === 'Photo'
                        && $view->getUserId() === null
                        && $view->getDateTime() instanceof \DateTime;
                }),
                true
            );

        $handler = new CountViewMessageHandler($viewStoragePersister);
        $handler($message);
    }

    public function testHandleMessageWithUser(): void
    {
        $dateTime = new \Carbon\Carbon('2024-01-15 14:30:00');

        $message = new CountViewMessage(
            entityId: 123,
            entityClassName: 'Ride',
            userId: 456,
            dateTime: $dateTime
        );

        $viewStoragePersister = $this->createMock(ViewStoragePersisterInterface::class);
        $viewStoragePersister
            ->expects($this->once())
            ->method('storeView')
            ->with(
                $this->callback(function (View $view) {
                    return $view->getEntityId() === 123
                        && $view->getEntityClassName() === 'Ride'
                        && $view->getUserId() === 456
                        && $view->getDateTime() instanceof \DateTime;
                }),
                true
            );

        $handler = new CountViewMessageHandler($viewStoragePersister);
        $handler($message);
    }

    /**
     * @dataProvider entityClassNameProvider
     */
    public function testHandleMessageWithDifferentEntityTypes(string $entityClassName): void
    {
        $message = new CountViewMessage(
            entityId: 1,
            entityClassName: $entityClassName,
            userId: null,
            dateTime: new \Carbon\Carbon()
        );

        $viewStoragePersister = $this->createMock(ViewStoragePersisterInterface::class);
        $viewStoragePersister
            ->expects($this->once())
            ->method('storeView')
            ->with(
                $this->callback(function (View $view) use ($entityClassName) {
                    return $view->getEntityClassName() === $entityClassName;
                }),
                true
            );

        $handler = new CountViewMessageHandler($viewStoragePersister);
        $handler($message);
    }

    public static function entityClassNameProvider(): array
    {
        return [
            'Photo' => ['Photo'],
            'Ride' => ['Ride'],
            'City' => ['City'],
            'Track' => ['Track'],
            'Thread' => ['Thread'],
        ];
    }

    public function testHandlerFlushesImmediately(): void
    {
        $message = new CountViewMessage(
            entityId: 1,
            entityClassName: 'Photo',
            userId: null,
            dateTime: new \Carbon\Carbon()
        );

        $viewStoragePersister = $this->createMock(ViewStoragePersisterInterface::class);
        $viewStoragePersister
            ->expects($this->once())
            ->method('storeView')
            ->with(
                $this->isInstanceOf(View::class),
                true  // flush = true
            );

        $handler = new CountViewMessageHandler($viewStoragePersister);
        $handler($message);
    }
}
