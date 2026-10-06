<?php declare(strict_types=1);

namespace Tests\Entity;

use App\Entity\Location;
use App\Entity\Photo;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Die Lage von Orten und Fotos als Geometrie (Issues #1138, #1139).
 *
 * Dieselben zwei Fallen wie bei Stadt, Fahrt und Tourenmuster, und sie sind
 * hier nicht weniger still: In WKT steht die Laenge vor der Breite, und 0,0
 * ist im Bestand ein Platzhalter, kein Ort im Golf von Guinea. Bei den Fotos
 * betrifft das 18 Zeilen.
 */
class LocationAndPhotoCoordinatesTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    /**
     * @return array<string, array{0: class-string}>
     */
    public static function entities(): array
    {
        return [
            'Ort' => [Location::class],
            'Foto' => [Photo::class],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function tabellen(): array
    {
        return ['location' => ['location'], 'photo' => ['photo']];
    }

    /**
     * @param class-string $klasse
     */
    #[DataProvider('entities')]
    public function testTheSettersKeepTheGeometryInStep(string $klasse): void
    {
        $entity = new $klasse();
        self::assertNull($entity->getCoordinates(), 'Ohne Koordinaten keine Geometrie.');

        $entity->setLatitude(53.55);
        self::assertNull($entity->getCoordinates(), 'Eine Breite allein ergibt noch keinen Punkt.');

        $entity->setLongitude(9.99);
        self::assertSame('SRID=4326;POINT(9.99000000 53.55000000)', $entity->getCoordinates());
    }

    /**
     * @param class-string $klasse
     */
    #[DataProvider('entities')]
    public function testLongitudeComesFirst(string $klasse): void
    {
        $entity = new $klasse();
        $entity->setLatitude(53.55);
        $entity->setLongitude(9.99);

        self::assertStringStartsWith('SRID=4326;POINT(9.99', (string) $entity->getCoordinates());
        self::assertStringNotContainsString('POINT(53.55', (string) $entity->getCoordinates());
    }

    /**
     * @param class-string $klasse
     */
    #[DataProvider('entities')]
    public function testThePlaceholderZeroDoesNotBecomeAPoint(string $klasse): void
    {
        $entity = new $klasse();
        $entity->setLatitude(0.0);
        $entity->setLongitude(0.0);

        self::assertNull($entity->getCoordinates(), 'Null Grad, null Grad heisst hier: keine Angabe.');
    }

    /**
     * @param class-string $klasse
     */
    #[DataProvider('entities')]
    public function testClearingACoordinateClearsTheGeometry(string $klasse): void
    {
        $entity = new $klasse();
        $entity->setLatitude(53.55);
        $entity->setLongitude(9.99);
        self::assertNotNull($entity->getCoordinates());

        $entity->setLongitude(null);
        self::assertNull($entity->getCoordinates());
    }

    #[DataProvider('tabellen')]
    public function testEveryRowWithCoordinatesCarriesItsGeometry(string $tabelle): void
    {
        $this->nurMitPostGIS();

        $mitKoordinaten = (int) $this->entityManager->getConnection()->fetchOne(sprintf(
            'SELECT count(*) FROM %s WHERE latitude IS NOT NULL AND longitude IS NOT NULL
             AND latitude <> 0 AND longitude <> 0', $tabelle
        ));
        $mitGeometrie = (int) $this->entityManager->getConnection()->fetchOne(sprintf(
            'SELECT count(*) FROM %s WHERE coordinates IS NOT NULL', $tabelle
        ));

        self::assertSame($mitKoordinaten, $mitGeometrie,
            sprintf('Jede Zeile in %s mit Koordinaten hat auch eine Geometrie.', $tabelle));
    }

    #[DataProvider('tabellen')]
    public function testTheSpatialIndexIsAGist(string $tabelle): void
    {
        $this->nurMitPostGIS();

        $art = $this->entityManager->getConnection()->fetchOne(
            'SELECT am.amname FROM pg_index i
             JOIN pg_class c ON c.oid = i.indexrelid
             JOIN pg_am am ON am.oid = c.relam
             WHERE c.relname = :name',
            ['name' => $tabelle . '_coordinates_gist']
        );

        self::assertSame('gist', $art, sprintf('%s traegt einen GiST-Index.', $tabelle));
    }

    /**
     * Und die Geometrie steht auch da, wo sie hingehoert: Die zurueckgelesene
     * Lage muss der gespeicherten entsprechen. Ein vertauschtes Paar faellt
     * hier auf, wo es in den reinen Zeichenkettenpruefungen oben nur beim
     * Format auffiele.
     */
    public function testTheStoredPointMatchesTheFloatColumns(): void
    {
        $this->nurMitPostGIS();

        $zeile = $this->entityManager->getConnection()->fetchAssociative(
            'SELECT latitude, longitude,
                    ST_Y(coordinates) AS geo_breite, ST_X(coordinates) AS geo_laenge
             FROM photo WHERE coordinates IS NOT NULL LIMIT 1'
        );

        if (false === $zeile) {
            self::markTestSkipped('Keine Fotos mit Koordinaten in den Fixtures.');
        }

        self::assertEqualsWithDelta((float) $zeile['latitude'], (float) $zeile['geo_breite'], 0.0000001,
            'ST_Y liefert die Breite.');
        self::assertEqualsWithDelta((float) $zeile['longitude'], (float) $zeile['geo_laenge'], 0.0000001,
            'ST_X liefert die Laenge.');
    }

    private function nurMitPostGIS(): void
    {
        if (!$this->entityManager->getConnection()->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            self::markTestSkipped('Geometriespalten gibt es nur mit PostGIS.');
        }
    }
}
