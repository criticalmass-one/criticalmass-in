<?php declare(strict_types=1);

namespace App\Controller;

use App\Criticalmass\SeoPage\SeoPageInterface;
use App\Entity\Ride;
use App\Repository\RideRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Der Kalender: ein Monat auf einen Blick, ein Tag im Einzelnen.
 *
 * Die Touren verteilen sich extrem ungleich. Im September 2026 stehen an 28
 * Tagen zusammen 310 Touren — im Mittel zwei je Tag, aber **145 allein am
 * letzten Freitag**. Ein Raster, das jedem Tag gleich viel Platz gibt, muss
 * daran scheitern: Es verschenkt Flaeche an leere Tage und versteckt
 * ausgerechnet den einen Tag hinter einem Rollbalken.
 *
 * Deshalb zeigt das Raster nur noch, **ob** und **wie viel** an einem Tag los
 * ist; die Touren selbst stehen daneben. So muss nichts abgeschnitten werden.
 *
 * Ohne JavaScript funktioniert das genauso: Jeder Tag ist ein Verweis, und
 * der Server rendert den gewaehlten.
 */
class CalendarController extends AbstractController
{
    public function __construct(
        ManagerRegistry $managerRegistry,
        private readonly RideRepository $rideRepository,
    ) {
        parent::__construct($managerRegistry);
    }

    #[Route(
        '/calendar',
        name: 'caldera_criticalmass_calendar',
        requirements: ['year' => '\d{4}', 'month' => '\d{1,2}', 'day' => '\d{1,2}'],
        priority: 280
    )]
    public function indexAction(Request $request, SeoPageInterface $seoPage): Response
    {
        $monat = $this->monatAus($request);

        $touren = $this->rideRepository->findRides(
            $monat,
            $monat->modify('last day of this month')->setTime(23, 59, 59)
        );

        $nachTag = $this->nachTagOrdnen($touren);
        $gewaehlt = $this->gewaehlterTag($request, $monat, $nachTag);

        $seoPage->setDescription(sprintf(
            'Alle Critical-Mass-Touren im %s — %d Touren an %d Tagen.',
            $monat->format('F Y'),
            count($touren),
            count($nachTag)
        ));

        return $this->render('Calendar/index.html.twig', [
            'dateTime' => $monat,
            'previousMonth' => $monat->modify('-1 month'),
            'nextMonth' => $monat->modify('+1 month'),
            'tageImMonat' => (int) $monat->format('t'),
            // 0 = Montag, damit die Woche dort beginnt, wo sie hier beginnt.
            'ersterWochentag' => ((int) $monat->format('N')) - 1,
            'nachTag' => $nachTag,
            'gewaehlterTag' => $gewaehlt,
            'tourenGesamt' => count($touren),
        ]);
    }

    private function monatAus(Request $request): \DateTimeImmutable
    {
        $jetzt = new \DateTimeImmutable();
        $laufend = $jetzt->modify('first day of this month')->setTime(0, 0);

        $jahr = $this->zahlAus($request, 'year');
        $monat = $this->zahlAus($request, 'month');

        if (null === $jahr || null === $monat
            || $monat < 1 || $monat > 12 || $jahr < 1990 || $jahr > 2100) {
            return $laufend;
        }

        return (new \DateTimeImmutable(sprintf('%04d-%02d-01', $jahr, $monat)))->setTime(0, 0);
    }

    /**
     * Liest einen Zahlparameter, ohne an Unsinn zu zerbrechen.
     *
     * `$request->query->getInt()` **wirft** bei einem nicht-numerischen Wert,
     * statt die Vorgabe zu nehmen — `/calendar?year=abc` endete damit in einem
     * 500er. Ein Bot oder ein alter Zeiger reicht dafuer.
     */
    private function zahlAus(Request $request, string $name): ?int
    {
        $wert = $request->query->get($name);

        return is_numeric($wert) ? (int) $wert : null;
    }

    /**
     * @param array<int, Ride> $touren
     *
     * @return array<int, array<int, Ride>> Tag im Monat => Touren, nach Uhrzeit
     */
    private function nachTagOrdnen(array $touren): array
    {
        $nachTag = [];

        foreach ($touren as $tour) {
            $zeitpunkt = $tour->getDateTime();

            if (null === $zeitpunkt) {
                continue;
            }

            $nachTag[(int) $zeitpunkt->format('j')][] = $tour;
        }

        foreach ($nachTag as &$eintraege) {
            usort($eintraege, static fn (Ride $a, Ride $b): int => $a->getDateTime() <=> $b->getDateTime());
        }

        ksort($nachTag);

        return $nachTag;
    }

    /**
     * Welcher Tag rechts steht, wenn keiner gewaehlt wurde.
     *
     * Heute, wenn der Monat der laufende ist — sonst der erste Tag, an dem
     * ueberhaupt etwas stattfindet. Ein leerer Tag als Begruessung waere die
     * unfreundlichste aller Vorgaben.
     *
     * @param array<int, array<int, Ride>> $nachTag
     */
    private function gewaehlterTag(Request $request, \DateTimeImmutable $monat, array $nachTag): int
    {
        $tage = (int) $monat->format('t');
        $gewuenscht = $this->zahlAus($request, 'day') ?? 0;

        if ($gewuenscht >= 1 && $gewuenscht <= $tage) {
            return $gewuenscht;
        }

        $jetzt = new \DateTimeImmutable();

        if ($jetzt->format('Y-m') === $monat->format('Y-m')) {
            return (int) $jetzt->format('j');
        }

        return [] === $nachTag ? 1 : (int) array_key_first($nachTag);
    }
}
