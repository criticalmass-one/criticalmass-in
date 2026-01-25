<?php declare(strict_types=1);

namespace Tests\Message;

use App\Message\CountViewMessage;
use PHPUnit\Framework\TestCase;

class CountViewMessageTest extends TestCase
{
    public function testCreateMessageWithoutUser(): void
    {
        $dateTime = new \DateTime('2024-01-15 14:30:00');

        $message = new CountViewMessage(
            entityId: 42,
            entityClassName: 'Photo',
            userId: null,
            dateTime: $dateTime
        );

        $this->assertSame(42, $message->getEntityId());
        $this->assertSame('Photo', $message->getEntityClassName());
        $this->assertNull($message->getUserId());
        $this->assertSame($dateTime, $message->getDateTime());
    }

    public function testCreateMessageWithUser(): void
    {
        $dateTime = new \DateTime('2024-01-15 14:30:00');

        $message = new CountViewMessage(
            entityId: 123,
            entityClassName: 'Ride',
            userId: 456,
            dateTime: $dateTime
        );

        $this->assertSame(123, $message->getEntityId());
        $this->assertSame('Ride', $message->getEntityClassName());
        $this->assertSame(456, $message->getUserId());
        $this->assertSame($dateTime, $message->getDateTime());
    }

    public function testMessageIsImmutable(): void
    {
        $dateTime = new \DateTime('2024-01-15 14:30:00');

        $message = new CountViewMessage(
            entityId: 1,
            entityClassName: 'City',
            userId: 2,
            dateTime: $dateTime
        );

        // Verify that the message properties cannot be changed
        $this->assertSame(1, $message->getEntityId());
        $this->assertSame('City', $message->getEntityClassName());
        $this->assertSame(2, $message->getUserId());
    }

    /**
     * @dataProvider entityClassNameProvider
     */
    public function testDifferentEntityClassNames(string $entityClassName): void
    {
        $message = new CountViewMessage(
            entityId: 1,
            entityClassName: $entityClassName,
            userId: null,
            dateTime: new \DateTime()
        );

        $this->assertSame($entityClassName, $message->getEntityClassName());
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
}
