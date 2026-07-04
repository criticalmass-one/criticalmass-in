<?php declare(strict_types=1);

namespace Tests\EventSubscriber;

use App\EventSubscriber\SecurityHeadersEventSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class SecurityHeadersEventSubscriberTest extends TestCase
{
    private function dispatch(Request $request, ?Response $response = null): Response
    {
        $response ??= new Response();

        $event = new ResponseEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $response,
        );

        (new SecurityHeadersEventSubscriber())->onResponse($event);

        return $response;
    }

    public function testSetsBaselineAndReportOnlyCsp(): void
    {
        $response = $this->dispatch(Request::create('http://example.org/'));

        self::assertSame('SAMEORIGIN', $response->headers->get('X-Frame-Options'));
        self::assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        self::assertStringContainsString("default-src 'self'", (string) $response->headers->get('Content-Security-Policy-Report-Only'));
        self::assertStringContainsString("object-src 'none'", (string) $response->headers->get('Content-Security-Policy-Report-Only'));
    }

    public function testHstsOnlyOnSecureRequest(): void
    {
        $insecure = $this->dispatch(Request::create('http://example.org/'));
        self::assertFalse($insecure->headers->has('Strict-Transport-Security'));

        $secure = $this->dispatch(Request::create('https://example.org/'));
        self::assertStringContainsString('max-age=', (string) $secure->headers->get('Strict-Transport-Security'));
        self::assertStringContainsString('includeSubDomains', (string) $secure->headers->get('Strict-Transport-Security'));
    }

    public function testDoesNotOverrideExistingEnforcingCsp(): void
    {
        $response = new Response();
        $response->headers->set('Content-Security-Policy', "default-src 'none'");

        $result = $this->dispatch(Request::create('http://example.org/'), $response);

        self::assertFalse($result->headers->has('Content-Security-Policy-Report-Only'));
        self::assertSame("default-src 'none'", $result->headers->get('Content-Security-Policy'));
    }
}
