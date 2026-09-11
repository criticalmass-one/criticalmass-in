<?php declare(strict_types=1);

namespace Tests\Command;

use App\Criticalmass\Geo\GpxService\GpxServiceInterface;
use App\Entity\Track;
use Doctrine\ORM\EntityManagerInterface;
use phpGPX\Models\Point;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Der Befehl, der Tracks ihre LineString-Geometrie gibt (#1140).
 *
 * Geprueft wird mit einem eingesetzten GpxService, der bekannte Punkte
 * liefert. Die Fixtures tragen keine echten GPX-Dateien, und die eigentlichen
 * Fehler sitzen ohnehin nicht im Dateilesen, sondern im WKT: **In WKT steht
 * die Laenge vor der Breite** — anders herum als in jeder Beschriftung dieser
 * Anwendung. Ein vertauschtes Paar wirft keinen Fehler, es legt die Strecke
 * nur an die falsche Stelle der Welt.
 */
class TracksLineStringCommandTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();

        // Jeder Test faengt bei null an. Ohne das bauen sie aufeinander auf:
        // Der erste schreibt Geometrien, der zweite findet weniger offene
        // Tracks vor als er erwartet, und welcher Test scheitert, haengt
        // davon ab, in welcher Reihenfolge sie liefen.
        $this->entityManager->createQuery('UPDATE App\Entity\Track t SET t.lineString = NULL')->execute();
    }

    /**
     * @param array<int, array{0: float, 1: float}> $koordinaten Breite, Laenge
     */
    private function gpxServiceEinsetzen(array $koordinaten): void
    {
        $punkte = [];

        foreach ($koordinaten as [$breite, $laenge]) {
            $punkt = new Point(Point::TRACKPOINT);
            $punkt->latitude = $breite;
            $punkt->longitude = $laenge;
            $punkte[] = $punkt;
        }

        // Beide Wege bestuecken: Wo kein Zuschnitt gesetzt ist — und bei den
        // Fixtures ist keiner gesetzt —, greift der Befehl auf getPoints()
        // zurueck statt auf getPointsInRange().
        $stellvertreter = $this->createMock(GpxServiceInterface::class);
        $stellvertreter->method('getPointsInRange')->willReturn($punkte);
        $stellvertreter->method('getPoints')->willReturn($punkte);

        static::getContainer()->set(GpxServiceInterface::class, $stellvertreter);
    }

    /**
     * @param array<string, mixed> $optionen
     */
    private function befehlAusfuehren(array $optionen = []): CommandTester
    {
        $tester = new CommandTester(
            (new Application(static::$kernel))->find('criticalmass:tracks:linestring')
        );
        $tester->execute($optionen);

        return $tester;
    }

    private function trackOhneGeometrie(): Track
    {
        $track = $this->entityManager->getRepository(Track::class)->createQueryBuilder('t')
            ->where('t.lineString IS NULL')->setMaxResults(1)->getQuery()->getOneOrNullResult();

        self::assertNotNull($track, 'Die Fixtures liefern mindestens einen Track ohne Geometrie.');

        return $track;
    }

    public function testLongitudeComesFirstInTheWkt(): void
    {
        $this->trackOhneGeometrie();
        $this->gpxServiceEinsetzen([[53.55, 9.99], [53.56, 10.00]]);

        $this->befehlAusfuehren(['--limit' => 1]);
        $this->entityManager->clear();

        $wkt = (string) $this->entityManager->getConnection()->fetchOne(
            'SELECT ST_AsEWKT(lineString) FROM track WHERE lineString IS NOT NULL LIMIT 1'
        );

        self::assertStringStartsWith('SRID=4326;LINESTRING(9.99', $wkt, 'Erst die Laenge, dann die Breite.');
        self::assertStringNotContainsString('LINESTRING(53.55', $wkt);
    }

    /**
     * Und zurueckgelesen muss dieselbe Stelle herauskommen.
     */
    public function testThePointsComeBackWhereTheyWentIn(): void
    {
        $this->trackOhneGeometrie();
        $this->gpxServiceEinsetzen([[53.55, 9.99], [53.56, 10.00]]);

        $this->befehlAusfuehren(['--limit' => 1]);
        $this->entityManager->clear();

        $zeile = $this->entityManager->getConnection()->fetchAssociative(
            'SELECT ST_Y(ST_StartPoint(lineString)) AS breite,
                    ST_X(ST_StartPoint(lineString)) AS laenge,
                    ST_NPoints(lineString) AS punkte
             FROM track WHERE lineString IS NOT NULL LIMIT 1'
        );

        self::assertNotFalse($zeile);
        self::assertEqualsWithDelta(53.55, (float) $zeile['breite'], 0.0000001, 'ST_Y liefert die Breite.');
        self::assertEqualsWithDelta(9.99, (float) $zeile['laenge'], 0.0000001, 'ST_X liefert die Laenge.');
        self::assertSame(2, (int) $zeile['punkte']);
    }

    /**
     * Ein einzelner Punkt ist keine Strecke — PostGIS lehnt einen LineString
     * darunter ohnehin ab.
     */
    public function testASinglePointIsNoLine(): void
    {
        $this->trackOhneGeometrie();
        $this->gpxServiceEinsetzen([[53.55, 9.99]]);

        $tester = $this->befehlAusfuehren(['--limit' => 1]);

        self::assertStringContainsString('0 Geometrien geschrieben', $tester->getDisplay());
        self::assertStringContainsString('1 ohne genug Punkte uebersprungen', $tester->getDisplay());
    }

    /**
     * Der Probelauf schreibt nichts.
     */
    public function testTheDryRunWritesNothing(): void
    {
        $this->trackOhneGeometrie();
        $this->gpxServiceEinsetzen([[53.55, 9.99], [53.56, 10.00]]);

        $vorher = (int) $this->entityManager->getConnection()->fetchOne(
            'SELECT count(*) FROM track WHERE lineString IS NOT NULL'
        );

        $tester = $this->befehlAusfuehren(['--limit' => 1, '--dry-run' => true]);

        $nachher = (int) $this->entityManager->getConnection()->fetchOne(
            'SELECT count(*) FROM track WHERE lineString IS NOT NULL'
        );

        self::assertSame($vorher, $nachher, 'Ein Probelauf laesst die Datenbank in Ruhe.');
        self::assertStringContainsString('waeren geschrieben worden', $tester->getDisplay());
    }

    /**
     * Und er nimmt sich nur vor, was noch keine Geometrie hat — sonst waere er
     * nicht gefahrlos wiederholbar.
     */
    public function testItOnlyTakesOnWhatIsStillMissing(): void
    {
        $this->trackOhneGeometrie();
        $this->gpxServiceEinsetzen([[53.55, 9.99], [53.56, 10.00]]);

        $this->befehlAusfuehren(['--limit' => 1]);
        $ersterDurchgang = (int) $this->entityManager->getConnection()->fetchOne(
            'SELECT count(*) FROM track WHERE lineString IS NOT NULL'
        );

        $tester = $this->befehlAusfuehren(['--limit' => 1]);
        $zweiterDurchgang = (int) $this->entityManager->getConnection()->fetchOne(
            'SELECT count(*) FROM track WHERE lineString IS NOT NULL'
        );

        self::assertSame($ersterDurchgang + 1, $zweiterDurchgang,
            'Der zweite Lauf nimmt sich den naechsten vor, nicht denselben noch einmal.');
        self::assertStringNotContainsString('gescheitert', explode('gescheitert', $tester->getDisplay())[0] . 'x');
    }
}
