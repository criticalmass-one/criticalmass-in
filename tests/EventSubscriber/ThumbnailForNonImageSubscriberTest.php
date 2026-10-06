<?php declare(strict_types=1);

namespace Tests\EventSubscriber;

use App\EventSubscriber\ThumbnailForNonImageSubscriber;
use Liip\ImagineBundle\Exception\LogicException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Von einer Datei, die kein Bild ist, gibt es keine Vorschau — das ist ein
 * 404, kein Serverfehler.
 *
 * Der Bestand ist erledigt (Version20260906100000), aber alte Vorschau-
 * Adressen aus Suchindizes und Verlaeufen treffen die Dateien weiterhin
 * direkt; Liip fragt die Datenbank nicht.
 */
final class ThumbnailForNonImageSubscriberTest extends TestCase
{
    private function ereignis(\Throwable $fehler, ?string $route): ExceptionEvent
    {
        $request = Request::create('/media/cache/resolve/gallery_photo_thumb/video.mov');

        if (null !== $route) {
            $request->attributes->set('_route', $route);
        }

        return new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            $request,
            HttpKernelInterface::MAIN_REQUEST,
            $fehler,
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
    public function testTheMimeTypeComplaintBecomesANotFound(string $route): void
    {
        $ereignis = $this->ereignis(
            new LogicException('The mime type of file x.mov must be image/xxx or application/pdf, got video/quicktime.'),
            $route
        );

        (new ThumbnailForNonImageSubscriber())($ereignis);

        self::assertInstanceOf(NotFoundHttpException::class, $ereignis->getThrowable());
    }

    /**
     * Die urspruengliche Ausnahme bleibt als Ursache erhalten — sonst waere
     * im Protokoll nicht mehr zu sehen, worum es ging.
     */
    public function testTheOriginalCauseIsKept(): void
    {
        $urspruenglich = new LogicException('The mime type of file x.mov must be image/xxx or application/pdf.');
        $ereignis = $this->ereignis($urspruenglich, 'liip_imagine_filter');

        (new ThumbnailForNonImageSubscriber())($ereignis);

        self::assertSame($urspruenglich, $ereignis->getThrowable()->getPrevious());
    }

    /**
     * Ausserhalb der Liip-Routen wird nichts angefasst — eine LogicException
     * aus einer fehlerhaften Filterkonfiguration soll weiterhin auffallen.
     */
    public function testItKeepsItsHandsOffOtherRoutes(): void
    {
        $urspruenglich = new LogicException('irgendetwas anderes');
        $ereignis = $this->ereignis($urspruenglich, 'caldera_criticalmass_frontpage');

        (new ThumbnailForNonImageSubscriber())($ereignis);

        self::assertSame($urspruenglich, $ereignis->getThrowable());
    }

    public function testARequestWithoutARouteIsLeftAlone(): void
    {
        $urspruenglich = new LogicException('irgendetwas anderes');
        $ereignis = $this->ereignis($urspruenglich, null);

        (new ThumbnailForNonImageSubscriber())($ereignis);

        self::assertSame($urspruenglich, $ereignis->getThrowable());
    }

    /**
     * Und andere Fehler auf derselben Route bleiben, was sie sind.
     */
    public function testOtherFailuresOnTheSameRouteStayAsTheyAre(): void
    {
        $urspruenglich = new \RuntimeException('Festplatte voll');
        $ereignis = $this->ereignis($urspruenglich, 'liip_imagine_filter');

        (new ThumbnailForNonImageSubscriber())($ereignis);

        self::assertSame($urspruenglich, $ereignis->getThrowable());
    }
}
