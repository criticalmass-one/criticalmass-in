<?php declare(strict_types=1);

namespace Tests\Entity;

use App\Entity\City;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Die Lage einer Stadt als Geometrie (Issue #1136).
 *
 * Zwei Dinge sind hier leicht falsch zu machen und beide fallen nicht auf, wenn
 * man sie nicht prueft: In WKT steht die **Laenge vor der Breite**, anders
 * herum als in jeder Beschriftung dieser Anwendung. Und der Punkt 0,0 ist
 * geografisch echt — er liegt im Golf von Guinea —, im Bestand aber ein
 * Platzhalter fuer "keine Angabe".
 */
class CityCoordinatesTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    private function istPostgreSql(): bool
    {
        return $this->entityManager->getConnection()->getDatabasePlatform() instanceof PostgreSQLPlatform;
    }

    public function testTheSettersKeepTheGeometryInStep(): void
    {
        $city = new City();
        self::assertNull($city->getCoordinates(), 'Ohne Koordinaten keine Geometrie.');

        $city->setLatitude(53.55);
        self::assertNull($city->getCoordinates(), 'Eine Breite allein ergibt noch keinen Punkt.');

        $city->setLongitude(9.99);
        self::assertSame('SRID=4326;POINT(9.99000000 53.55000000)', $city->getCoordinates());
    }

    /**
     * Der Fehler, der sonst unbemerkt bliebe: vertauschte Reihenfolge. Haetten
     * Laenge und Breite den Platz getauscht, laege Hamburg im Indischen Ozean —
     * und keine Zusicherung ausser dieser wuerde es merken.
     */
    public function testLongitudeComesFirstInTheGeometry(): void
    {
        // Nicht verkettet: Die Setter geben die Schnittstelle zurueck, nicht die
        // Stadt — und die Schnittstelle kennt die Geometrie noch nicht.
        $city = new City();
        $city->setLatitude(53.55);
        $city->setLongitude(9.99);

        self::assertStringStartsWith('SRID=4326;POINT(9.99', (string) $city->getCoordinates());
        self::assertStringNotContainsString('POINT(53.55', (string) $city->getCoordinates());
    }

    public function testThePlaceholderZeroDoesNotBecomeAPoint(): void
    {
        $city = new City();
        $city->setLatitude(0.0);
        $city->setLongitude(0.0);
        self::assertNull($city->getCoordinates(), 'Null Grad, null Grad heisst hier: keine Angabe.');

        $halb = new City();
        $halb->setLatitude(53.55);
        $halb->setLongitude(0.0);
        self::assertNull($halb->getCoordinates());
    }

    public function testClearingACoordinateClearsTheGeometry(): void
    {
        $city = new City();
        $city->setLatitude(53.55);
        $city->setLongitude(9.99);
        self::assertNotNull($city->getCoordinates());

        $city->setLatitude(null);
        self::assertNull($city->getCoordinates(), 'Faellt eine Koordinate weg, faellt der Punkt mit.');
    }

    /**
     * Die Probe aufs Exempel: Der Wert muss durch die Datenbank kommen und dort
     * raeumlich abfragbar sein.
     */
    public function testTheGeometrySurvivesTheDatabaseAndCanBeQueried(): void
    {
        if (!$this->istPostgreSql()) {
            self::markTestSkipped('Geometriespalten gibt es nur mit PostGIS.');
        }

        $hamburg = $this->entityManager->getRepository(City::class)->findOneBy(['city' => 'Hamburg']);
        self::assertInstanceOf(City::class, $hamburg);
        self::assertNotNull($hamburg->getLatitude());

        $hamburg->setLatitude($hamburg->getLatitude());
        $hamburg->setLongitude($hamburg->getLongitude());
        $this->entityManager->flush();
        $this->entityManager->refresh($hamburg);

        self::assertNotNull($hamburg->getCoordinates(), 'Die Geometrie steht in der Datenbank.');

        // Entfernung von der eigenen Stelle: null. Rechnet PostGIS mit dem
        // richtigen Punkt, kommt hier auch null heraus.
        $entfernung = (float) $this->entityManager->getConnection()->fetchOne(
            'SELECT ST_Distance(coordinates::geography, ST_SetSRID(ST_MakePoint(:lon, :lat), 4326)::geography)
             FROM city WHERE id = :id',
            ['lon' => $hamburg->getLongitude(), 'lat' => $hamburg->getLatitude(), 'id' => $hamburg->getId()]
        );

        self::assertLessThan(1.0, $entfernung, 'Der gespeicherte Punkt ist der, den die Stadt angibt.');
    }

    public function testTheSpatialIndexExists(): void
    {
        if (!$this->istPostgreSql()) {
            self::markTestSkipped('Geometriespalten gibt es nur mit PostGIS.');
        }

        $art = $this->entityManager->getConnection()->fetchOne(
            "SELECT am.amname FROM pg_index i
             JOIN pg_class c ON c.oid = i.indexrelid
             JOIN pg_am am ON am.oid = c.relam
             WHERE c.relname = 'city_coordinates_gist'"
        );

        self::assertSame('gist', $art, 'Der Index ist ein GiST — ein B-Baum nuetzte der Umkreissuche nichts.');
    }
}
