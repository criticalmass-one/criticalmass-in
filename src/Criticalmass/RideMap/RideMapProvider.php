<?php declare(strict_types=1);

namespace App\Criticalmass\RideMap;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\City;
use App\Entity\Ride;
use App\Enum\RideTypeEnum;
use App\Repository\RideRepository;

/**
 * Die Touren eines Tages, aufbereitet fuer die Übersichtskarte.
 *
 * „Tag“ meint den Tag **am Ort der Tour**. `ride.dateTime` haelt UTC; wer
 * danach schneidet, schiebt eine Abendtour in Portland auf den Folgetag und
 * eine Morgentour in Jakarta auf den Vortag. Deshalb holt die Abfrage ein
 * Fenster, das jeden Ortstag der Welt abdeckt, und aussortiert wird danach in
 * der Zeitzone der jeweiligen Stadt.
 */
class RideMapProvider
{
    /** Fruehester Ortstag beginnt bei UTC+14, spaetester endet bei UTC−12. */
    private const VORLAUF = '-14 hours';
    private const NACHLAUF = '+36 hours';

    /**
     * Touren aus den Zyklen tragen keine Art — im vierten Quartal 2026 sind
     * das 765 von 824. Blieben sie leer, faende der Filter „Critical Mass“
     * ausgerechnet die regulaeren Critical Masses nicht. Die Zyklen
     * beschreiben Critical Masses, also zaehlt eine Tour ohne Angabe als eine.
     */
    private const ART_OHNE_ANGABE = RideTypeEnum::CRITICAL_MASS;

    public function __construct(
        private readonly RideRepository $rideRepository,
        private readonly ObjectRouterInterface $objectRouter,
    ) {
    }

    /**
     * @return list<array{
     *     id: int|null, city: string, title: string|null, date: string, time: string, location: string|null,
     *     latitude: float, longitude: float, type: string, active: bool, cancelled: bool, url: string|null, text: string
     * }>
     */
    public function forDay(\DateTimeImmutable $day): array
    {
        return $this->forRange($day, $day);
    }

    /**
     * Alle Touren vom ersten bis einschliesslich zum letzten Tag, jeweils am Ort gerechnet.
     *
     * @return list<array<string, mixed>>
     */
    public function forRange(\DateTimeImmutable $from, \DateTimeImmutable $until): array
    {
        $utc = new \DateTimeZone('UTC');
        $beginn = new \DateTimeImmutable($from->format('Y-m-d') . ' 00:00:00', $utc);
        $ende = new \DateTimeImmutable($until->format('Y-m-d') . ' 00:00:00', $utc);

        $touren = $this->rideRepository->findForMapBetween(
            \DateTime::createFromImmutable($beginn->modify(self::VORLAUF)),
            \DateTime::createFromImmutable($ende->modify(self::NACHLAUF))
        );

        $erster = $from->format('Y-m-d');
        $letzter = $until->format('Y-m-d');
        $eintraege = [];

        foreach ($touren as $tour) {
            $stadt = $tour->getCity();
            $zeitpunkt = $tour->getDateTime();

            if (null === $stadt || null === $zeitpunkt) {
                continue;
            }

            $ortszeit = \DateTimeImmutable::createFromInterface($zeitpunkt)
                ->setTimezone($this->zeitzone($stadt));

            $ortstag = $ortszeit->format('Y-m-d');

            if ($ortstag < $erster || $ortstag > $letzter) {
                continue;
            }

            $ort = $this->koordinate($tour, $stadt);

            if (null === $ort) {
                continue;
            }

            $eintraege[] = [
                'id' => $tour->getId(),
                'city' => (string) $stadt->getCity(),
                'title' => $tour->getTitle(),
                'date' => $ortstag,
                'time' => $ortszeit->format('H:i'),
                'location' => $tour->getLocation(),
                'latitude' => $ort[0],
                'longitude' => $ort[1],
                'type' => ($tour->getRideType() ?? self::ART_OHNE_ANGABE)->value,
                'active' => !$stadt->isInactive(),
                'cancelled' => !$tour->isEnabled(),
                'url' => $this->adresse($tour),
                'text' => $this->suchtext($tour, $stadt),
            ];
        }

        return $eintraege;
    }

    /**
     * Alles, wonach sich eine Tour finden lassen soll, in einem Stueck: Stadt,
     * Titel, Treffpunkt, Beschreibung und Tourart. Die Liste zeigt davon nur
     * einen Teil; durchsucht wird das Ganze.
     */
    private function suchtext(Ride $tour, City $stadt): string
    {
        $teile = [
            $stadt->getCity(),
            $stadt->getTitle(),
            $tour->getTitle(),
            $tour->getLocation(),
            $tour->getDescription(),
            ($tour->getRideType() ?? self::ART_OHNE_ANGABE)->label(),
        ];

        $text = implode(' ', array_filter($teile, static fn (?string $teil): bool => null !== $teil && '' !== $teil));

        // Beschreibungen tragen Markdown und vereinzelt HTML; fuer die Suche zaehlen nur die Woerter.
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5);

        return trim((string) preg_replace('/\s+/u', ' ', $text));
    }

    private function zeitzone(City $stadt): \DateTimeZone
    {
        try {
            return new \DateTimeZone($stadt->getTimezone() ?: 'Europe/Berlin');
        } catch (\Exception) {
            return new \DateTimeZone('Europe/Berlin');
        }
    }

    /**
     * Der Treffpunkt der Tour, ersatzweise die Stadtmitte.
     *
     * Viele Touren stehen ohne Treffpunkt im Kalender. Sie auf der Karte
     * wegzulassen hiesse, dass die Stadt an diesem Tag gar nicht erscheint.
     *
     * @return array{0: float, 1: float}|null
     */
    private function koordinate(Ride $tour, City $stadt): ?array
    {
        foreach ([[$tour->getLatitude(), $tour->getLongitude()], [$stadt->getLatitude(), $stadt->getLongitude()]] as [$breite, $laenge]) {
            if (null === $breite || null === $laenge) {
                continue;
            }

            $breite = (float) $breite;
            $laenge = (float) $laenge;

            // 0/0 ist in diesem Bestand kein Ort im Golf von Guinea, sondern ein leeres Feld.
            if (0.0 === $breite && 0.0 === $laenge) {
                continue;
            }

            return [$breite, $laenge];
        }

        return null;
    }

    private function adresse(Ride $tour): ?string
    {
        try {
            return $this->objectRouter->generate($tour);
        } catch (\Throwable) {
            return null;
        }
    }
}
