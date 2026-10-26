<?php declare(strict_types=1);

namespace Tests\EventSubscriber;

use App\EventSubscriber\PasskeyRateLimitSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

/**
 * Wie im ApiRateLimitSubscriberTest: echte RateLimiterFactory (final) mit
 * In-Memory-Storage und limit 1, der IP-Bucket wird gezielt vorab erschöpft.
 */
final class PasskeyRateLimitSubscriberTest extends TestCase
{
    private const CLIENT_IP = '127.0.0.1';

    public function testThrottlesLoginOptionsWhenLimitExceeded(): void
    {
        $factory = $this->factory();
        $factory->create(self::CLIENT_IP)->consume();

        $this->expectException(TooManyRequestsHttpException::class);
        (new PasskeyRateLimitSubscriber($factory))->onKernelRequest($this->event('/passkey/login/options'));
    }

    public function testThrottlesRegistrationWhenLimitExceeded(): void
    {
        $factory = $this->factory();
        $factory->create(self::CLIENT_IP)->consume();

        $this->expectException(TooManyRequestsHttpException::class);
        (new PasskeyRateLimitSubscriber($factory))->onKernelRequest($this->event('/passkey/register'));
    }

    public function testAllowsRequestWithinLimit(): void
    {
        (new PasskeyRateLimitSubscriber($this->factory()))->onKernelRequest($this->event('/passkey/login'));

        $this->addToAssertionCount(1);
    }

    /**
     * Der Magic-Link-Login hat seine eigene Drosselung und darf hier nicht mit
     * hineingezogen werden — /login beginnt nicht mit /passkey/.
     */
    public function testIgnoresOtherLoginRoutes(): void
    {
        $factory = $this->factory();
        $factory->create(self::CLIENT_IP)->consume();

        (new PasskeyRateLimitSubscriber($factory))->onKernelRequest($this->event('/login'));

        $this->addToAssertionCount(1);
    }

    public function testIgnoresSubRequests(): void
    {
        $factory = $this->factory();
        $factory->create(self::CLIENT_IP)->consume();

        $event = new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            Request::create('/passkey/login/options', 'POST'),
            HttpKernelInterface::SUB_REQUEST,
        );

        (new PasskeyRateLimitSubscriber($factory))->onKernelRequest($event);

        $this->addToAssertionCount(1);
    }

    private function factory(): RateLimiterFactory
    {
        return new RateLimiterFactory(
            ['id' => 'passkey_test', 'policy' => 'fixed_window', 'limit' => 1, 'interval' => '1 minute'],
            new InMemoryStorage(),
        );
    }

    private function event(string $path): RequestEvent
    {
        return new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            Request::create($path, 'POST'),
            HttpKernelInterface::MAIN_REQUEST,
        );
    }
}
