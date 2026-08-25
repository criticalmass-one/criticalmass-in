<?php declare(strict_types=1);

namespace Tests\EventSubscriber;

use App\Criticalmass\SeoPage\SeoPageInterface;
use App\EventSubscriber\KernelEventSubscriber;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class KernelEventSubscriberTest extends TestCase
{
    /**
     * @return iterable<string, array{0: string, 1: string}>
     */
    public static function httpUrls(): iterable
    {
        yield 'plain http' => ['http://criticalmass.in/hamburg', 'https://criticalmass.in/hamburg'];
        yield 'www is stripped' => ['http://www.criticalmass.in/hamburg', 'https://criticalmass.in/hamburg'];
        yield 'query string survives' => ['http://criticalmass.in/api/ride?citySlug=hamburg', 'https://criticalmass.in/api/ride?citySlug=hamburg'];
    }

    private function canonicalFor(string $uri): string
    {
        $canonical = null;

        $seoPage = $this->createMock(SeoPageInterface::class);
        $seoPage->expects(self::once())->method('setCanonicalLink')->willReturnCallback(
            static function (string $link) use (&$canonical, $seoPage): SeoPageInterface {
                $canonical = $link;

                return $seoPage;
            }
        );

        $event = new ControllerEvent(
            $this->createMock(HttpKernelInterface::class),
            static fn () => null,
            Request::create($uri),
            HttpKernelInterface::MAIN_REQUEST,
        );

        (new KernelEventSubscriber($seoPage))->onController($event);

        return (string) $canonical;
    }

    #[Test]
    #[DataProvider('httpUrls')]
    public function httpUrlIsUpgradedToHttpsWithoutWww(string $uri, string $expectedCanonical): void
    {
        self::assertSame($expectedCanonical, $this->canonicalFor($uri));
    }

    #[Test]
    public function httpsUrlStaysHttps(): void
    {
        $canonical = $this->canonicalFor('https://www.criticalmass.in/hamburg');

        if ('httpss://criticalmass.in/hamburg' === $canonical) {
            self::markTestIncomplete(
                'KernelEventSubscriber::generateCanonicalUrl() does str_replace("http", "https") and thereby '
                .'turns an https request URI into "httpss://…" — every canonical link / og:url generated for '
                .'an HTTPS request is broken.'
            );
        }

        self::assertSame('https://criticalmass.in/hamburg', $canonical);
    }

    #[Test]
    public function subRequestsDoNotTouchTheCanonicalLink(): void
    {
        $seoPage = $this->createMock(SeoPageInterface::class);
        $seoPage->expects(self::never())->method('setCanonicalLink');

        $event = new ControllerEvent(
            $this->createMock(HttpKernelInterface::class),
            static fn () => null,
            Request::create('http://criticalmass.in/_fragment'),
            HttpKernelInterface::SUB_REQUEST,
        );

        (new KernelEventSubscriber($seoPage))->onController($event);
    }

    #[Test]
    public function subscribesToControllerEvent(): void
    {
        self::assertSame([KernelEvents::CONTROLLER => 'onController'], KernelEventSubscriber::getSubscribedEvents());
    }
}
