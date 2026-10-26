<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Setzt Nara und Salvador dorthin, wo sie wirklich liegen.
 *
 * Bei den 80 Staedten mit unpassender Zeitzone (Version20260908120000) fielen
 * diese zwei aus der Reihe: Dort ist nicht die Zeitzone falsch, sondern die
 * Koordinate. Beide sehen nach einem verunglueckten Geocoding aus, das den
 * Stadtnamen an der falschen Stelle der Welt gefunden hat:
 *
 *   Nara       traegt Asia/Tokyo, lag aber auf 38.8927, -77.0229 —
 *              das ist Washington D.C., nahe dem Kapitol.
 *   Salvador   traegt America/Bahia, lag aber auf 13.8000, -88.9141 —
 *              das ist El Salvador, also das Land, nicht die Stadt in Bahia.
 *
 * Die Zeitzonen sind spezifisch genug, um sie als bewusst gesetzt zu lesen:
 * `America/Bahia` waehlt niemand versehentlich fuer El Salvador. Beide neuen
 * Koordinaten sind gegen dieselbe Zonendatenbank geprueft und landen in
 * genau der Zone, die schon hinterlegt ist — deshalb bleiben die Zeitzonen
 * hier unberuehrt.
 *
 * Die Bedingung nennt die alten Werte: Wer inzwischen von Hand nachgebessert
 * hat, wird nicht ueberfahren.
 */
final class Version20260908130000 extends AbstractMigration
{
    /**
     * ID => [alte Breite, alte Laenge, neue Breite, neue Laenge, Ort]
     *
     * @return array<int, array{0: float, 1: float, 2: float, 3: float, 4: string}>
     */
    private function zuordnung(): array
    {
        return [
            // Nara, Japan — Stadtzentrum, statt Washington D.C.
            1002 => [38.8927368, -77.0229201, 34.6851, 135.8048, 'Nara'],

            // Salvador, Bahia, Brasilien — statt El Salvador.
            1020 => [13.8000382, -88.9140683, -12.9777, -38.5016, 'Salvador'],
        ];
    }

    public function getDescription(): string
    {
        return 'Move Nara and Salvador to where they actually are';
    }

    public function up(Schema $schema): void
    {
        foreach ($this->zuordnung() as $id => [$alteBreite, $alteLaenge, $neueBreite, $neueLaenge]) {
            $this->koordinateSetzen($schema, $id, $alteBreite, $alteLaenge, $neueBreite, $neueLaenge);
        }
    }

    public function down(Schema $schema): void
    {
        foreach ($this->zuordnung() as $id => [$alteBreite, $alteLaenge, $neueBreite, $neueLaenge]) {
            $this->koordinateSetzen($schema, $id, $neueBreite, $neueLaenge, $alteBreite, $alteLaenge);
        }
    }

    /**
     * Setzt die Koordinate — und die Geometrie gleich mit, falls es sie schon
     * gibt.
     *
     * Die Geometriespalte wird sonst von den Settern der Entity nachgefuehrt
     * (#1136); rohes SQL geht daran vorbei und liesse sie auf dem alten Punkt
     * stehen. Ob die Spalte existiert, haengt davon ab, ob #1138/#1139 hier
     * schon durchgelaufen sind — deshalb die Abfrage statt einer Annahme.
     */
    private function koordinateSetzen(
        Schema $schema,
        int $id,
        float $vonBreite,
        float $vonLaenge,
        float $nachBreite,
        float $nachLaenge
    ): void {
        $this->addSql(
            'UPDATE city SET latitude = :nachBreite, longitude = :nachLaenge
             WHERE id = :id AND latitude = :vonBreite AND longitude = :vonLaenge',
            [
                'nachBreite' => $nachBreite,
                'nachLaenge' => $nachLaenge,
                'id' => $id,
                'vonBreite' => $vonBreite,
                'vonLaenge' => $vonLaenge,
            ]
        );

        if ($schema->getTable('city')->hasColumn('coordinates')) {
            // In WKT steht die Laenge vor der Breite.
            $this->addSql(
                'UPDATE city SET coordinates = ST_SetSRID(ST_MakePoint(:nachLaenge, :nachBreite), 4326)
                 WHERE id = :id',
                ['nachLaenge' => $nachLaenge, 'nachBreite' => $nachBreite, 'id' => $id]
            );
        }
    }
}
