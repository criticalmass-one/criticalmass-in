<?php declare(strict_types=1);

namespace App\Controller;

use App\Criticalmass\RideMap\RideMapProvider;
use App\Enum\RideTypeEnum;
use App\Form\Type\RideMapFilterType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Die Übersichtskarte: alle Touren eines Tages auf einer Karte.
 *
 * Der Kalender beantwortet „wann ist wo etwas los“, die Karte „was ist an
 * diesem Tag in meiner Naehe“. Die Seite selbst liefert nur den Rahmen und die
 * Filter; die Touren eines Tages holt der Browser in einem Stueck und filtert
 * sie danach selbst — Suche, Tourart und Kartenausschnitt kosten so keine
 * weitere Anfrage. Nur ein anderes Datum oder ein anderer Zeitraum laedt neu.
 */
class RideMapController extends AbstractController
{
    private const DATUM = '\d{4}-\d{2}-\d{2}';

    /** Laenger als ein Monat wird die Karte nicht: Das waeren schnell ueber tausend Touren. */
    private const HOECHSTENS_TAGE = 31;

    #[Route('/map', name: 'caldera_criticalmass_ride_map', methods: ['GET'], priority: 280)]
    public function indexAction(Request $request, FormFactoryInterface $formFactory): Response
    {
        // all() statt get(): get() wirft bei ?date[]=x, statt die Vorgabe zu nehmen.
        $anfrage = $request->query->all();
        $tag = $this->tagAus($anfrage['date'] ?? null) ?? $this->heute();

        $form = $formFactory->createNamed('', RideMapFilterType::class, [
            'date' => $tag,
            'range' => in_array($anfrage['range'] ?? null, RideMapFilterType::ZEITRAEUME, true) ? $anfrage['range'] : 'day',
            'q' => $this->textAus($request, 'q'),
            // Vorgabe ist „nur aktive“; abgewaehlt steht ausdruecklich active=0 in der Adresse.
            'active' => '0' !== ($anfrage['active'] ?? null),
            'type' => $this->tourartenAus($request),
        ]);

        return $this->render('RideMap/index.html.twig', [
            'form' => $form,
            'tag' => $tag,
        ]);
    }

    #[Route(
        '/map/rides/{date}/{until}',
        name: 'caldera_criticalmass_ride_map_rides',
        requirements: ['date' => self::DATUM, 'until' => self::DATUM],
        methods: ['GET'],
        priority: 280
    )]
    public function ridesAction(string $date, RideMapProvider $rideMapProvider, ?string $until = null): JsonResponse
    {
        $von = $this->tagAus($date);
        $bis = null === $until ? $von : $this->tagAus($until);

        if (null === $von || null === $bis || $bis < $von || $von->diff($bis)->days >= self::HOECHSTENS_TAGE) {
            throw $this->createNotFoundException();
        }

        $response = new JsonResponse([
            'date' => $von->format('Y-m-d'),
            'until' => $bis->format('Y-m-d'),
            'rides' => $rideMapProvider->forRange($von, $bis),
        ]);

        // Ein Tagesbestand aendert sich selten; wer zwischen zwei Tagen hin- und
        // herblaettert, soll nicht jedes Mal neu fragen.
        $response->setPublic();
        $response->setMaxAge(120);

        return $response;
    }

    /**
     * Liest ein Datum, ohne an Unsinn zu zerbrechen: `2026-02-31` und `abc`
     * ergeben beide null statt einer Ausnahme oder eines stillen Ueberlaufs
     * in den Maerz.
     */
    private function tagAus(mixed $wert): ?\DateTimeImmutable
    {
        if (!is_string($wert) || 1 !== preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $wert, $teile)) {
            return null;
        }

        [$jahr, $monat, $tag] = [(int) $teile[1], (int) $teile[2], (int) $teile[3]];

        if ($jahr < 1990 || $jahr > 2100 || !checkdate($monat, $tag, $jahr)) {
            return null;
        }

        return new \DateTimeImmutable(sprintf('%04d-%02d-%02d 00:00:00', $jahr, $monat, $tag));
    }

    private function heute(): \DateTimeImmutable
    {
        // time() statt new DateTime(), damit ClockMock in Tests greift.
        return (new \DateTimeImmutable())->setTimestamp(time())->setTime(0, 0);
    }

    private function textAus(Request $request, string $name): ?string
    {
        $wert = $request->query->all()[$name] ?? null;

        return is_string($wert) && '' !== trim($wert) ? mb_substr(trim($wert), 0, 100) : null;
    }

    /** @return list<RideTypeEnum> */
    private function tourartenAus(Request $request): array
    {
        $werte = $request->query->all()['type'] ?? [];
        $tourarten = [];

        foreach (is_array($werte) ? $werte : [$werte] as $wert) {
            $tourart = is_string($wert) ? RideTypeEnum::tryFrom($wert) : null;

            if (null !== $tourart && !in_array($tourart, $tourarten, true)) {
                $tourarten[] = $tourart;
            }
        }

        return $tourarten;
    }
}
