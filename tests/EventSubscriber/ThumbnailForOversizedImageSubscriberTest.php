<?php declare(strict_types=1);

namespace Tests\EventSubscriber;

use App\EventSubscriber\ThumbnailForOversizedImageSubscriber;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

// Imagick ist weder lokal noch in der CI installiert -- nur auf dem Server.
require_once __DIR__ . '/../Stub/imagick_exception.php';

/**
 * Ein Bild, das ImageMagick nicht oeffnen darf, ergibt 404 statt 500.
 *
 * Drei Panoramen im Bestand sind 16382 Pixel breit; policy.xml deckelt Breite
 * und Hoehe bei 16000. Jeder Aufruf der Galerie warf dadurch einen 500er.
 * Heben laesst sich die Grenze aus der Anwendung heraus nicht -- also bleibt,
 * ehrlich zu antworten: Unter dieser Richtlinie *gibt* es keine Vorschau.
 */
final class ThumbnailForOversizedImageSubscriberTest extends TestCase
{
    private function ereignis(\Throwable $fehler, ?string $route): ExceptionEvent
    {
        $request = Request::create('/media/cache/resolve/gallery_photo_thumb/panorama.jpg');

        if (null !== $route) {
            $request->attributes->set('_route', $route);
            $request->attributes->set('path', 'panorama.jpg');
            $request->attributes->set('filter', 'gallery_photo_thumb');
        }

        return new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $fehler,
        );
    }

    private function subscriber(?LoggerInterface $logger = null): ThumbnailForOversizedImageSubscriber
    {
        return new ThumbnailForOversizedImageSubscriber($logger ?? new NullLogger());
    }

    /**
     * Genau die Kette, die Liip erzeugt: Controller-RuntimeException um die des
     * Imagine-Adapters um die ImagickException.
     */
    private function echteKette(): \Throwable
    {
        $imagick = new \ImagickException('width or height exceeds limit `panorama.jpg\'', 465);
        $imagine = new \RuntimeException('Could not load image from string', 0, $imagick);

        return new \RuntimeException(
            'Unable to create image for path "panorama.jpg" and filter "gallery_photo_thumb".',
            0,
            $imagine
        );
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function liipRouten(): array
    {
        return [
            'Filter' => ['liip_imagine_filter'],
            'Filter zur Laufzeit' => ['liip_imagine_filter_runtime'],
        ];
    }

    #[DataProvider('liipRouten')]
    public function testAnImageMagickRefusalBecomesANotFound(string $route): void
    {
        $ereignis = $this->ereignis($this->echteKette(), $route);

        ($this->subscriber())($ereignis);

        self::assertInstanceOf(NotFoundHttpException::class, $ereignis->getThrowable());
    }

    /**
     * Auch wenn die ImagickException ganz oben steht -- auf die Tiefe der
     * Verpackung soll sich niemand verlassen muessen.
     */
    public function testTheDepthOfTheWrappingDoesNotMatter(): void
    {
        $ereignis = $this->ereignis(
            new \ImagickException('width or height exceeds limit', 465),
            'liip_imagine_filter'
        );

        ($this->subscriber())($ereignis);

        self::assertInstanceOf(NotFoundHttpException::class, $ereignis->getThrowable());
    }

    /**
     * Der urspruengliche Fehler bleibt als Ursache erhalten -- sonst waere im
     * Protokoll nicht mehr zu sehen, worum es ging.
     */
    public function testTheOriginalFailureIsKeptAsTheCause(): void
    {
        $ursprung = $this->echteKette();
        $ereignis = $this->ereignis($ursprung, 'liip_imagine_filter');

        ($this->subscriber())($ereignis);

        self::assertSame($ursprung, $ereignis->getThrowable()->getPrevious());
    }

    /**
     * Und er verschwindet nicht stillschweigend: Ein neues uebergrosses Bild
     * soll auffallen, nur eben nicht als Stoerung.
     */
    public function testTheRefusalIsWrittenToTheLog(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::once())
            ->method('warning')
            ->with(
                self::stringContains('Keine Vorschau'),
                self::callback(static fn (array $zusatz): bool => 'panorama.jpg' === $zusatz['pfad']
                    && 'gallery_photo_thumb' === $zusatz['filter']
                    && str_contains($zusatz['grund'], 'exceeds limit'))
            );

        ($this->subscriber($logger))($this->ereignis($this->echteKette(), 'liip_imagine_filter'));
    }

    /**
     * Ausserhalb der Liip-Routen wird nichts angefasst. Dieselbe Ausnahme aus
     * einem Bildbearbeitungsschritt anderswo soll weiterhin als Fehler
     * auffallen.
     */
    public function testOtherRoutesAreLeftAlone(): void
    {
        foreach (['criticalmass_photo_show', null] as $route) {
            $ursprung = $this->echteKette();
            $ereignis = $this->ereignis($ursprung, $route);

            ($this->subscriber())($ereignis);

            self::assertSame($ursprung, $ereignis->getThrowable(), sprintf('Route %s', $route ?? '(keine)'));
        }
    }

    /**
     * Und ein Filter, der aus einem anderen Grund scheitert -- ein fehlendes
     * Wasserzeichen etwa -- bleibt ein Fehler.
     */
    public function testAFailureWithoutImageMagickStaysAnError(): void
    {
        $ursprung = new \RuntimeException(
            'Unable to create image for path "foto.jpg" and filter "gallery_photo_standard".',
            0,
            new \RuntimeException('Watermark image not found')
        );

        $ereignis = $this->ereignis($ursprung, 'liip_imagine_filter');

        ($this->subscriber())($ereignis);

        self::assertSame($ursprung, $ereignis->getThrowable());
    }
}
