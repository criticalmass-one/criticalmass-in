<?php declare(strict_types=1);

namespace Tests\EventSubscriber;

use App\EventSubscriber\CorsEventSubscriber;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class CorsEventSubscriberTest extends TestCase
{
    private function dispatch(Request $request, ?Response $response = null): Response
    {
        $response ??= new Response('payload');

        $event = new ResponseEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response,
        );

        (new CorsEventSubscriber())->onResponse($event);

        return $response;
    }

    #[Test]
    public function subscribesToResponseEvent(): void
    {
        self::assertSame([KernelEvents::RESPONSE => 'onResponse'], CorsEventSubscriber::getSubscribedEvents());
    }

    #[Test]
    public function addsCorsHeadersToApiGetRequests(): void
    {
        $response = $this->dispatch(Request::create('/api/city'));

        self::assertSame('*', $response->headers->get('Access-Control-Allow-Origin'));
        self::assertSame('GET, OPTIONS', $response->headers->get('Access-Control-Allow-Methods'));
        self::assertSame('Content-Type', $response->headers->get('Access-Control-Allow-Headers'));
        self::assertSame('3600', $response->headers->get('Access-Control-Max-Age'));
        self::assertSame('payload', $response->getContent());
        self::assertSame(200, $response->getStatusCode());
    }

    #[Test]
    public function preflightRequestsGetAnEmptyNoContentResponse(): void
    {
        $response = $this->dispatch(Request::create('/api/city', 'OPTIONS'));

        self::assertSame(204, $response->getStatusCode());
        self::assertSame('', $response->getContent());
        self::assertSame('*', $response->headers->get('Access-Control-Allow-Origin'));
    }

    #[Test]
    public function nonApiPathsAreLeftAlone(): void
    {
        $response = $this->dispatch(Request::create('/hamburg'));

        self::assertFalse($response->headers->has('Access-Control-Allow-Origin'));
    }

    #[Test]
    public function apiPrefixMustBeAFullPathSegment(): void
    {
        $response = $this->dispatch(Request::create('/apiary'));

        self::assertFalse($response->headers->has('Access-Control-Allow-Origin'));
    }

    #[Test]
    public function writingMethodsGetNoCorsHeaders(): void
    {
        foreach (['POST', 'PUT', 'DELETE', 'PATCH'] as $method) {
            $response = $this->dispatch(Request::create('/api/city', $method));

            self::assertFalse($response->headers->has('Access-Control-Allow-Origin'), $method);
        }
    }
}
