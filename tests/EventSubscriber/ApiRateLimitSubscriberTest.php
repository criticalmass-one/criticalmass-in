<?php declare(strict_types=1);

namespace Tests\EventSubscriber;

use App\EventSubscriber\ApiRateLimitSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

/**
 * Echte RateLimiterFactory (final) mit In-Memory-Storage, limit 1: durch
 * gezieltes Vor-Erschöpfen des IP-Buckets lässt sich sowohl die Drosselung als
 * auch das Überspringen (Reads / Nicht-API) ohne Mocking nachweisen.
 */
final class ApiRateLimitSubscriberTest extends TestCase
{
    private const CLIENT_IP = '127.0.0.1';

    private function factory(): RateLimiterFactory
    {
        return new RateLimiterFactory(
            ['id' => 'api_write_test', 'policy' => 'fixed_window', 'limit' => 1, 'interval' => '1 minute'],
            new InMemoryStorage(),
        );
    }

    private function event(string $method, string $path): RequestEvent
    {
        return new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            Request::create($path, $method),
            HttpKernelInterface::MAIN_REQUEST,
        );
    }

    public function testThrottlesApiWriteWhenLimitExceeded(): void
    {
        $factory = $this->factory();
        // Bucket der Client-IP vorab erschöpfen.
        $factory->create(self::CLIENT_IP)->consume();

        $this->expectException(TooManyRequestsHttpException::class);
        (new ApiRateLimitSubscriber($factory))->onKernelRequest($this->event('POST', '/api/estimate'));
    }

    public function testAllowsApiWriteWithinLimit(): void
    {
        (new ApiRateLimitSubscriber($this->factory()))->onKernelRequest($this->event('POST', '/api/estimate'));
        $this->addToAssertionCount(1);
    }

    public function testIgnoresApiReadRequests(): void
    {
        $factory = $this->factory();
        $factory->create(self::CLIENT_IP)->consume();

        // Bucket ist erschöpft — würde der Subscriber Reads verarbeiten, gäbe es 429.
        (new ApiRateLimitSubscriber($factory))->onKernelRequest($this->event('GET', '/api/ride'));
        $this->addToAssertionCount(1);
    }

    public function testIgnoresNonApiPaths(): void
    {
        $factory = $this->factory();
        $factory->create(self::CLIENT_IP)->consume();

        (new ApiRateLimitSubscriber($factory))->onKernelRequest($this->event('POST', '/login'));
        $this->addToAssertionCount(1);
    }
}
