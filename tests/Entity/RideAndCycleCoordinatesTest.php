<?php declare(strict_types=1);

namespace Tests\Entity;

use App\Entity\CityCycle;
use App\Entity\Ride;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Die Lage von Fahrten und Tourenmustern als Geometrie (Issues #1137, #1142).
 *
 * Dieselben zwei Fallen wie bei den Staedten, und sie sind hier nicht weniger
 * still: In WKT steht die Laenge vor der Breite, und 0,0 ist im Bestand ein
 * Platzhalter, kein Ort im Golf von Guinea.
 */
class RideAndCycleCoordinatesTest extends KernelTestCase
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
            'Fahrt' => [Ride::class],
            'Tourenmuster' => [CityCycle::class],
        ];
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

    public function testTheRidesFromTheFixturesCarryTheirGeometry(): void
    {
        if (!$this->entityManager->getConnection()->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            self::markTestSkipped('Geometriespalten gibt es nur mit PostGIS.');
        }

        $mitKoordinaten = (int) $this->entityManager->getConnection()->fetchOne(
            'SELECT count(*) FROM ride WHERE latitude IS NOT NULL AND longitude IS NOT NULL
             AND latitude <> 0 AND longitude <> 0'
        );
        $mitGeometrie = (int) $this->entityManager->getConnection()->fetchOne(
            'SELECT count(*) FROM ride WHERE coordinates IS NOT NULL'
        );

        self::assertSame($mitKoordinaten, $mitGeometrie, 'Jede Fahrt mit Koordinaten hat auch eine Geometrie.');
    }

    #[DataProvider('tabellen')]
    public function testTheSpatialIndexIsAGist(string $tabelle): void
    {
        if (!$this->entityManager->getConnection()->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            self::markTestSkipped('Geometriespalten gibt es nur mit PostGIS.');
        }

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
     * @return array<string, array{0: string}>
     */
    public static function tabellen(): array
    {
        return ['ride' => ['ride'], 'city_cycle' => ['city_cycle']];
    }
}
