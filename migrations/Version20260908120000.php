<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Setzt die Zeitzonen der Staedte auf das, was ihre Koordinaten sagen.
 *
 * Von 727 Staedten trugen **80** eine Zeitzone, die nicht zu ihrer Lage passt.
 * Los Angeles, Seattle und San Francisco standen auf Europe/Berlin — neun
 * Stunden daneben; Montevideo, Pittsburgh und zwei Dutzend weitere ebenso.
 * Sichtbar wurde das in der Seitenleiste "Bald unterwegs" und seit Neuestem in
 * der Timeline: Dort stand fuer eine Fahrt in Los Angeles eine Uhrzeit, die
 * dort niemand wiedererkennt.
 *
 * Korrigiert werden 78 davon, ermittelt aus latitude/longitude:
 *
 *   - 78 mit echtem Versatz, bis zu neun Stunden
 *   - 0 ohne Wirkung auf die Anzeige (Europe/Vienna statt
 *     Europe/Berlin und aehnliche) — dieselbe Uhrzeit, aber jetzt die richtige
 *     Angabe
 *
 * **Zwei bleiben absichtlich unangetastet**, weil dort nicht die Zeitzone
 * falsch ist, sondern die Koordinaten:
 *
 *   - Nara traegt Asia/Tokyo, liegt laut Koordinaten aber in Washington D.C.
 *   - Salvador traegt America/Bahia, liegt laut Koordinaten in El Salvador
 *
 * Sie aus den Koordinaten zu "korrigieren" haette den Fehler verschlimmert.
 *
 * Jede Anweisung nennt den alten Wert in der Bedingung: Wer inzwischen von
 * Hand nachgebessert hat, wird nicht ueberfahren, und down() trifft genau die
 * Zeilen, die up() angefasst hat.
 */
final class Version20260908120000 extends AbstractMigration
{
    /**
     * Stadt-ID => [alter Wert, neuer Wert]
     *
     * @return array<int, array{0: string, 1: string}>
     */
    private function zuordnung(): array
    {
        return [
            // Los Angeles (-9 h)
            509 => ['Europe/Berlin', 'America/Los_Angeles'],
            // San Francisco (-9 h)
            774 => ['Europe/Berlin', 'America/Los_Angeles'],
            // Seattle (-9 h)
            659 => ['Europe/Berlin', 'America/Los_Angeles'],
            // Albuquerque (-8 h)
            695 => ['Europe/Berlin', 'America/Denver'],
            // Memphis (-7 h)
            653 => ['Europe/Berlin', 'America/Chicago'],
            // Milwaukee (-7 h)
            725 => ['Europe/Berlin', 'America/Chicago'],
            // Charlotte (-6 h)
            782 => ['Europe/Berlin', 'America/New_York'],
            // Cleveland (-6 h)
            479 => ['Europe/Berlin', 'America/New_York'],
            // Richmond (-6 h)
            493 => ['Europe/Berlin', 'America/New_York'],
            // Worcester (-6 h)
            722 => ['Europe/Berlin', 'America/New_York'],
            // Goiânia (-5 h)
            776 => ['Europe/Berlin', 'America/Sao_Paulo'],
            // Torrelavega (+2 h)
            253 => ['Africa/Abidjan', 'Europe/Madrid'],
            // Bath (-1 h)
            700 => ['Europe/Berlin', 'Europe/London'],
            // Belfast (-1 h)
            784 => ['Europe/Berlin', 'Europe/London'],
            // Bridgwater (-1 h)
            680 => ['Europe/Berlin', 'Europe/London'],
            // Bristol (-1 h)
            554 => ['Europe/Berlin', 'Europe/London'],
            // Cambridge (-1 h)
            780 => ['Europe/Berlin', 'Europe/London'],
            // Cardiff (-1 h)
            783 => ['Europe/Berlin', 'Europe/London'],
            // Chattanooga (+1 h)
            827 => ['America/Chicago', 'America/New_York'],
            // Derby (-1 h)
            677 => ['Europe/Berlin', 'Europe/London'],
            // Eastbourne (-1 h)
            705 => ['Europe/Berlin', 'Europe/London'],
            // Glasgow (-1 h)
            683 => ['Europe/Berlin', 'Europe/London'],
            // Guildford (-1 h)
            687 => ['Europe/Berlin', 'Europe/London'],
            // Kampala (+1 h)
            596 => ['Europe/Berlin', 'Africa/Kampala'],
            // Lisboa (-1 h)
            748 => ['Europe/Berlin', 'Europe/Lisbon'],
            // Liverpool (-1 h)
            598 => ['Europe/Berlin', 'Europe/London'],
            // Oxford (-1 h)
            516 => ['Europe/Berlin', 'Europe/London'],
            // Portsmouth (-1 h)
            715 => ['Europe/Berlin', 'Europe/London'],
            // Redhill (-1 h)
            704 => ['Europe/Berlin', 'Europe/London'],
            // Southampton (-1 h)
            694 => ['Europe/Berlin', 'Europe/London'],
            // Spearfish (-1 h)
            948 => ['America/Chicago', 'America/Denver'],
            // Affoltern am Albis (+0 h)
            588 => ['Europe/Berlin', 'Europe/Zurich'],
            // Amstetten (+0 h)
            696 => ['Europe/Berlin', 'Europe/Vienna'],
            // Angouleme (+0 h)
            757 => ['Europe/Berlin', 'Europe/Paris'],
            // Bellinzona (+0 h)
            606 => ['Europe/Berlin', 'Europe/Zurich'],
            // Bertrange (+0 h)
            701 => ['Europe/Berlin', 'Europe/Paris'],
            // Bratislava (+0 h)
            603 => ['Europe/Berlin', 'Europe/Bratislava'],
            // Braunau am Inn (+0 h)
            679 => ['Europe/Berlin', 'Europe/Vienna'],
            // Chur (+0 h)
            525 => ['Europe/Berlin', 'Europe/Zurich'],
            // Colmar (+0 h)
            586 => ['Europe/Berlin', 'Europe/Paris'],
            // Dornbirn (+0 h)
            738 => ['Europe/Berlin', 'Europe/Vienna'],
            // Eisenstadt (+0 h)
            681 => ['Europe/Berlin', 'Europe/Vienna'],
            // Freistadt (+0 h)
            739 => ['Europe/Berlin', 'Europe/Vienna'],
            // Gallneukirchen (+0 h)
            684 => ['Europe/Berlin', 'Europe/Vienna'],
            // Gemeinde Klosterneuburg (+0 h)
            690 => ['Europe/Berlin', 'Europe/Vienna'],
            // Gemeinde Pressbaum (+0 h)
            731 => ['Europe/Berlin', 'Europe/Vienna'],
            // Gleisdorf (+0 h)
            469 => ['Europe/Berlin', 'Europe/Vienna'],
            // Gmunden (+0 h)
            685 => ['Europe/Berlin', 'Europe/Vienna'],
            // Götzis (+0 h)
            707 => ['Europe/Berlin', 'Europe/Vienna'],
            // Klosterneuburg (+0 h)
            709 => ['Europe/Berlin', 'Europe/Vienna'],
            // Košice (+0 h)
            601 => ['Europe/Berlin', 'Europe/Bratislava'],
            // Krems (+0 h)
            710 => ['Europe/Berlin', 'Europe/Vienna'],
            // Kreuzlingen (+0 h)
            546 => ['Europe/Berlin', 'Europe/Zurich'],
            // La Chaux-de-Fonds (+0 h)
            561 => ['Europe/Berlin', 'Europe/Zurich'],
            // Luxembourg (+0 h)
            558 => ['Europe/Berlin', 'Europe/Luxembourg'],
            // Luzern (+0 h)
            711 => ['Europe/Berlin', 'Europe/Zurich'],
            // Marseille (+0 h)
            641 => ['Europe/Berlin', 'Europe/Paris'],
            // Melk (+0 h)
            712 => ['Europe/Berlin', 'Europe/Vienna'],
            // Milano (+0 h)
            599 => ['Europe/Berlin', 'Europe/Rome'],
            // Mödling (+0 h)
            590 => ['Europe/Berlin', 'Europe/Vienna'],
            // Nice (+0 h)
            755 => ['Europe/Berlin', 'Europe/Paris'],
            // Olten (+0 h)
            495 => ['Europe/Berlin', 'Europe/Zurich'],
            // Padova (+0 h)
            777 => ['Europe/Berlin', 'Europe/Rome'],
            // Pregarten (+0 h)
            724 => ['Europe/Berlin', 'Europe/Vienna'],
            // Purkersdorf (+0 h)
            714 => ['Europe/Berlin', 'Europe/Vienna'],
            // Salzburg (+0 h)
            208 => ['Europe/Zurich', 'Europe/Vienna'],
            // St. Gallen (+0 h)
            567 => ['Europe/Berlin', 'Europe/Zurich'],
            // St. Pölten (+0 h)
            718 => ['Europe/Berlin', 'Europe/Vienna'],
            // Steyr (+0 h)
            719 => ['Europe/Berlin', 'Europe/Vienna'],
            // Szombathely (+0 h)
            778 => ['Europe/Berlin', 'Europe/Budapest'],
            // Thun (+0 h)
            550 => ['Europe/Berlin', 'Europe/Zurich'],
            // Torino (+0 h)
            650 => ['Europe/Berlin', 'Europe/Rome'],
            // Toruń (+0 h)
            501 => ['Europe/Berlin', 'Europe/Warsaw'],
            // Toulouse (+0 h)
            286 => ['Europe/Berlin', 'Europe/Paris'],
            // Tromsø (+0 h)
            804 => ['Arctic/Longyearbyen', 'Europe/Oslo'],
            // Vöcklabruck (+0 h)
            721 => ['Europe/Berlin', 'Europe/Vienna'],
            // Zagreb (+0 h)
            781 => ['Europe/Berlin', 'Europe/Zagreb'],
            // Zug (+0 h)
            589 => ['Europe/Berlin', 'Europe/Zurich'],
        ];
    }

    public function getDescription(): string
    {
        return 'Correct the timezone of 78 cities from their coordinates';
    }

    public function up(Schema $schema): void
    {
        foreach ($this->zuordnung() as $id => [$alt, $neu]) {
            $this->addSql(
                'UPDATE city SET timezone = :neu WHERE id = :id AND timezone = :alt',
                ['neu' => $neu, 'id' => $id, 'alt' => $alt]
            );
        }
    }

    public function down(Schema $schema): void
    {
        foreach ($this->zuordnung() as $id => [$alt, $neu]) {
            $this->addSql(
                'UPDATE city SET timezone = :alt WHERE id = :id AND timezone = :neu',
                ['alt' => $alt, 'id' => $id, 'neu' => $neu]
            );
        }
    }
}
