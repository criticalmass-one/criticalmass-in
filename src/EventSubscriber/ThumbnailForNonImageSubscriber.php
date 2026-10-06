<?php declare(strict_types=1);

namespace App\EventSubscriber;

use Liip\ImagineBundle\Exception\LogicException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Beantwortet die Vorschau einer Datei, die kein Bild ist, mit 404 statt 500.
 *
 * Liips DataManager wirft eine LogicException, sobald der MIME-Typ weder
 * `image/…` noch `application/pdf` ist. Fuer den Bestand ist das erledigt —
 * Version20260906100000 hat die 18 Nicht-Bilder aus dem Verkehr genommen, und
 * seither faellt statt 326 Fehlern in zwei Monaten noch einer an. Der kommt
 * von direkten Abrufen alter Vorschau-Adressen: Liip fragt die Datenbank
 * nicht, ein Zeiger aus einem Suchindex oder dem Verlauf trifft die Datei
 * weiterhin.
 *
 * Es *gibt* keine Vorschau einer Videodatei — 404 ist die richtige Auskunft,
 * nicht ein Serverfehler. Liips eigener Controller verfaehrt bei fehlenden
 * Dateien genauso (NotLoadableException wird zu NotFoundHttpException).
 *
 * Bewusst eng gefasst: nur auf den beiden Liip-Routen. Eine LogicException aus
 * einer fehlerhaften Filterkonfiguration soll weiterhin auffallen.
 */
#[AsEventListener(event: KernelEvents::EXCEPTION)]
final class ThumbnailForNonImageSubscriber
{
    private const LIIP_ROUTEN = ['liip_imagine_filter', 'liip_imagine_filter_runtime'];

    public function __invoke(ExceptionEvent $event): void
    {
        if (!$event->getThrowable() instanceof LogicException) {
            return;
        }

        $route = $event->getRequest()->attributes->get('_route');

        if (!\in_array($route, self::LIIP_ROUTEN, true)) {
            return;
        }

        $event->setThrowable(new NotFoundHttpException(
            'Von dieser Datei gibt es keine Vorschau.',
            $event->getThrowable()
        ));
    }
}
