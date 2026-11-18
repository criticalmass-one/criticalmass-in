<?php declare(strict_types=1);

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Beantwortet die Vorschau eines Bildes, das ImageMagick nicht oeffnen darf,
 * mit 404 statt 500.
 *
 * Drei Panoramen im Bestand sind **16382 Pixel breit** -- gestitchte
 * Rundumbilder derselben Kamera. ImageMagicks policy.xml deckelt Breite und
 * Hoehe bei 16KP, also 16000:
 *
 *     <policy domain="resource" name="width" value="16KP"/>
 *
 * Damit scheitert schon das Oeffnen der Datei, lange vor dem Verkleinern:
 * "width or height exceeds limit" @ error/cache.c/OpenPixelCache. Jeder
 * Aufruf der zugehoerigen Galerie warf einen 500er, zuletzt am 05.09. und am
 * 11.09.2026.
 *
 * **Das laesst sich in der Anwendung nicht abstellen.** Die Grenzen aus
 * policy.xml sind eine Decke, keine Vorgabe: Imagick::setResourceLimit() darf
 * sie senken, nicht heben -- ein Versuch bleibt wirkungslos, ohne Fehler. Wer
 * Vorschauen fuer diese drei Bilder will, muss entweder die Richtlinie des
 * Servers anfassen (die gilt fuer alle Anwendungen darauf) oder die Dateien
 * verkleinern.
 *
 * Was diese Klasse leistet, ist deshalb bescheidener und trotzdem das
 * Wichtigste: Sie macht aus dem Serverfehler eine ehrliche Auskunft. Es
 * *gibt* unter dieser Richtlinie keine Vorschau dieser Datei -- 404 sagt das,
 * 500 behauptet faelschlich, der Server sei kaputt. Der Schwesterfall steht in
 * {@see ThumbnailForNonImageSubscriber}, der dasselbe fuer Videos tut.
 *
 * Damit der Vorgang nicht stillschweigend verschwindet, geht eine Warnung ins
 * Protokoll: Ein neues uebergrosses Bild soll auffallen, nur eben nicht als
 * Stoerung.
 *
 * Bewusst eng gefasst: nur auf den beiden Liip-Routen, und nur, wenn in der
 * Kette tatsaechlich eine ImagickException steht. Ein Filter, der aus einem
 * anderen Grund scheitert, soll weiterhin als Fehler auffallen.
 */
#[AsEventListener(event: KernelEvents::EXCEPTION)]
final class ThumbnailForOversizedImageSubscriber
{
    private const LIIP_ROUTEN = ['liip_imagine_filter', 'liip_imagine_filter_runtime'];

    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function __invoke(ExceptionEvent $event): void
    {
        $route = $event->getRequest()->attributes->get('_route');

        if (!\in_array($route, self::LIIP_ROUTEN, true)) {
            return;
        }

        $imagick = $this->imagickAusDerKette($event->getThrowable());

        if (null === $imagick) {
            return;
        }

        $this->logger->warning('Keine Vorschau moeglich: ImageMagick lehnt die Datei ab.', [
            'pfad' => $event->getRequest()->attributes->get('path'),
            'filter' => $event->getRequest()->attributes->get('filter'),
            // ImageMagicks eigene Meldung nennt den Grund und die Datei; die
            // geltenden Grenzen stehen in policy.xml und damit im Kopf dieser
            // Klasse -- sie aendern sich nicht von Anfrage zu Anfrage.
            'grund' => $imagick->getMessage(),
        ]);

        $event->setThrowable(new NotFoundHttpException(
            'Von dieser Datei gibt es keine Vorschau.',
            $event->getThrowable()
        ));
    }

    /**
     * Liip verpackt den Fehler zweimal: eine RuntimeException des Controllers
     * um eine des Imagine-Adapters um die eigentliche ImagickException. Die
     * Tiefe ist nichts, worauf man sich festlegen sollte -- also die Kette
     * entlanggehen.
     */
    private function imagickAusDerKette(\Throwable $throwable): ?\Throwable
    {
        if (!class_exists(\ImagickException::class)) {
            return null;
        }

        for ($aktuell = $throwable; null !== $aktuell; $aktuell = $aktuell->getPrevious()) {
            if ($aktuell instanceof \ImagickException) {
                return $aktuell;
            }
        }

        return null;
    }
}
