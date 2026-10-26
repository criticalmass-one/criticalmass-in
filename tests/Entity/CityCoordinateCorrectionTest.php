<?php declare(strict_types=1);

namespace Tests\Entity;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Die beiden Staedte, bei denen nicht die Zeitzone falsch war, sondern die
 * Koordinate (Version20260908130000).
 *
 * Nara trug Asia/Tokyo und lag auf 38.89, -77.02 — Washington D.C. Salvador
 * trug America/Bahia und lag auf 13.80, -88.91 — El Salvador, also das Land
 * statt der Stadt in Brasilien. Beides sieht nach einem Geocoding aus, das
 * den Namen an der falschen Stelle der Welt gefunden hat.
 *
 * Beim Korrigieren solcher Werte gibt es genau zwei Arten, es wieder falsch
 * zu machen, und keine davon wirft einen Fehler: Breite und Laenge zu
 * vertauschen, oder ein Vorzeichen zu verlieren. Beide legen die Stadt
 * stillschweigend woandershin.
 */
final class CityCoordinateCorrectionTest extends TestCase
{
    /**
     * Grosszuegige Kaesten um die Laender — sie sollen keine Genauigkeit
     * pruefen, sondern die zwei Fehlerarten oben abfangen. Ein vertauschtes
     * Paar oder ein verlorenes Vorzeichen faellt hier heraus.
     *
     * @return array<string, array{0: float, 1: float, 2: array{0: float, 1: float, 2: float, 3: float}}>
     */
    public static function korrigierteStaedte(): array
    {
        return [
            // Nara liegt in Japan.
            'Nara' => [34.6851, 135.8048, [30.0, 46.0, 128.0, 146.0]],
            // Salvador liegt in Bahia, Brasilien — Suedhalbkugel, westlich von Greenwich.
            'Salvador' => [-12.9777, -38.5016, [-34.0, 6.0, -74.0, -34.0]],
        ];
    }

    /**
     * @param array{0: float, 1: float, 2: float, 3: float} $kasten
     */
    #[DataProvider('korrigierteStaedte')]
    public function testTheNewCoordinateLiesInTheRightCountry(float $breite, float $laenge, array $kasten): void
    {
        [$breiteVon, $breiteBis, $laengeVon, $laengeBis] = $kasten;

        self::assertGreaterThanOrEqual($breiteVon, $breite);
        self::assertLessThanOrEqual($breiteBis, $breite);
        self::assertGreaterThanOrEqual($laengeVon, $laenge);
        self::assertLessThanOrEqual($laengeBis, $laenge);
    }

    /**
     * Und die Werte, die tatsaechlich in der Migration stehen, sind dieselben —
     * sonst prueft dieser Test eine Absicht statt einer Aenderung.
     */
    public function testTheMigrationCarriesExactlyTheseValues(): void
    {
        $quelle = (string) file_get_contents(__DIR__ . '/../../migrations/Version20260908130000.php');

        preg_match_all(
            '/^\s*(\d+) => \[([-\d.]+), ([-\d.]+), ([-\d.]+), ([-\d.]+), \'([A-Za-z]+)\'\],/m',
            $quelle,
            $treffer,
            PREG_SET_ORDER
        );

        self::assertCount(2, $treffer, 'Die Migration korrigiert genau zwei Staedte.');

        $erwartet = self::korrigierteStaedte();

        foreach ($treffer as [, , , , $neueBreite, $neueLaenge, $name]) {
            self::assertArrayHasKey($name, $erwartet, sprintf('%s ist hier beschrieben.', $name));
            self::assertSame($erwartet[$name][0], (float) $neueBreite, sprintf('%s: Breite.', $name));
            self::assertSame($erwartet[$name][1], (float) $neueLaenge, sprintf('%s: Laenge.', $name));
        }
    }

    /**
     * Eine Breite jenseits von 90 Grad gibt es nicht — genau das entsteht,
     * wenn jemand Breite und Laenge vertauscht.
     */
    #[DataProvider('korrigierteStaedte')]
    public function testLatitudeAndLongitudeAreNotSwapped(float $breite, float $laenge): void
    {
        self::assertLessThanOrEqual(90.0, abs($breite), 'Eine Breite groesser als 90 Grad waere eine vertauschte Laenge.');
        self::assertLessThanOrEqual(180.0, abs($laenge));
    }

    /**
     * Und die neue Stelle liegt weit weg von der alten — waere sie es nicht,
     * haette die Korrektur nichts korrigiert.
     */
    public function testTheCitiesActuallyMoved(): void
    {
        $quelle = (string) file_get_contents(__DIR__ . '/../../migrations/Version20260908130000.php');

        preg_match_all(
            '/^\s*\d+ => \[([-\d.]+), ([-\d.]+), ([-\d.]+), ([-\d.]+), \'([A-Za-z]+)\'\],/m',
            $quelle,
            $treffer,
            PREG_SET_ORDER
        );

        self::assertNotEmpty($treffer);

        foreach ($treffer as [, $alteBreite, $alteLaenge, $neueBreite, $neueLaenge, $name]) {
            $entfernung = $this->entfernungKm(
                (float) $alteBreite, (float) $alteLaenge,
                (float) $neueBreite, (float) $neueLaenge
            );

            self::assertGreaterThan(
                1000.0,
                $entfernung,
                sprintf('%s liegt nun %d km entfernt — eine Korrektur, keine Verschiebung um die Ecke.', $name, (int) $entfernung)
            );
        }
    }

    private function entfernungKm(float $breite1, float $laenge1, float $breite2, float $laenge2): float
    {
        $erdradius = 6371.0;

        $dBreite = deg2rad($breite2 - $breite1);
        $dLaenge = deg2rad($laenge2 - $laenge1);

        $a = sin($dBreite / 2) ** 2
            + cos(deg2rad($breite1)) * cos(deg2rad($breite2)) * sin($dLaenge / 2) ** 2;

        return $erdradius * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
