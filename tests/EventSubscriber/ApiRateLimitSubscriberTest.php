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

    private function eventMitToken(string $method, string $path, string $token): RequestEvent
    {
        $request = Request::create($path, $method);
        $request->headers->set(ApiRateLimitSubscriber::TOKEN_HEADER, $token);

        return new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
        );
    }

    public function testThrottlesApiWriteWhenLimitExceeded(): void
    {
        $factory = $this->factory();
        // Bucket der Client-IP vorab erschöpfen.
        $factory->create(self::CLIENT_IP)->consume();

        $this->expectException(TooManyRequestsHttpException::class);
        (new ApiRateLimitSubscriber($factory, $this->factory()))->onKernelRequest($this->event('POST', '/api/estimate'));
    }

    public function testAllowsApiWriteWithinLimit(): void
    {
        (new ApiRateLimitSubscriber($this->factory(), $this->factory()))->onKernelRequest($this->event('POST', '/api/estimate'));
        $this->addToAssertionCount(1);
    }

    public function testIgnoresApiReadRequests(): void
    {
        $factory = $this->factory();
        $factory->create(self::CLIENT_IP)->consume();

        // Bucket ist erschöpft — würde der Subscriber Reads verarbeiten, gäbe es 429.
        (new ApiRateLimitSubscriber($factory, $this->factory()))->onKernelRequest($this->event('GET', '/api/ride'));
        $this->addToAssertionCount(1);
    }

    public function testIgnoresNonApiPaths(): void
    {
        $factory = $this->factory();
        $factory->create(self::CLIENT_IP)->consume();

        (new ApiRateLimitSubscriber($factory, $this->factory()))->onKernelRequest($this->event('POST', '/login'));
        $this->addToAssertionCount(1);
    }

    /**
     * Der Kern der Sache: Wer sich ausweist, faellt nicht unter die Bremse
     * fuer alle anderen — auch dann nicht, wenn deren Eimer laengst leer ist.
     */
    public function testOurOwnServiceGetsItsOwnAllowance(): void
    {
        $fuerAlle = $this->factory();
        $fuerUns = $this->factory();
        $fuerAlle->create(self::CLIENT_IP)->consume();

        $subscriber = new ApiRateLimitSubscriber($fuerAlle, $fuerUns, 'geheim');
        $subscriber->onKernelRequest($this->eventMitToken('POST', '/api/estimate', 'geheim'));

        $this->addToAssertionCount(1);
    }

    public function testAWrongTokenFallsBackToTheOrdinaryLimit(): void
    {
        $fuerAlle = $this->factory();
        $fuerAlle->create(self::CLIENT_IP)->consume();

        $subscriber = new ApiRateLimitSubscriber($fuerAlle, $this->factory(), 'geheim');

        $this->expectException(TooManyRequestsHttpException::class);
        $subscriber->onKernelRequest($this->eventMitToken('POST', '/api/estimate', 'falsch'));
    }

    /**
     * Ohne konfiguriertes Token ist niemand vertrauenswuerdig. Sonst wuerde
     * eine vergessene Konfiguration jeden Absender durchwinken, der die
     * Kopfzeile weglaesst.
     */
    public function testWithoutAConfiguredTokenNobodyIsTrusted(): void
    {
        $fuerAlle = $this->factory();
        $fuerAlle->create(self::CLIENT_IP)->consume();

        $subscriber = new ApiRateLimitSubscriber($fuerAlle, $this->factory(), '');

        $this->expectException(TooManyRequestsHttpException::class);
        $subscriber->onKernelRequest($this->event('POST', '/api/estimate'));
    }

    public function testAnEmptyHeaderIsNoMatchEither(): void
    {
        $fuerAlle = $this->factory();
        $fuerAlle->create(self::CLIENT_IP)->consume();

        $subscriber = new ApiRateLimitSubscriber($fuerAlle, $this->factory(), 'geheim');

        $this->expectException(TooManyRequestsHttpException::class);
        $subscriber->onKernelRequest($this->eventMitToken('POST', '/api/estimate', ''));
    }

    /**
     * Und das weite Kontingent ist keine Freikarte: Ist auch dieser Eimer
     * leer, greift die Bremse ebenfalls.
     */
    public function testEvenOurOwnServiceIsStoppedWhenItRunsAway(): void
    {
        $fuerUns = $this->factory();
        $fuerUns->create('eigener-dienst')->consume();

        $subscriber = new ApiRateLimitSubscriber($this->factory(), $fuerUns, 'geheim');

        $this->expectException(TooManyRequestsHttpException::class);
        $subscriber->onKernelRequest($this->eventMitToken('POST', '/api/estimate', 'geheim'));
    }
}
