<?php declare(strict_types=1);

namespace Tests\Doctrine;

use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManagerInterface;
use Jsor\Doctrine\PostGIS\Types\GeographyType;
use Jsor\Doctrine\PostGIS\Types\GeometryType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Das Fundament fuer die Geometrie-Issues #1136 bis #1142.
 *
 * Geprueft wird die Anbindung, nicht PostGIS selbst: dass Doctrine die
 * Geometrietypen kennt, dass der Schema-Filter die Tabellen von PostGIS in Ruhe
 * laesst, und dass die Extension da ist. Ohne diese drei Dinge scheitert jede
 * Geometriespalte, die spaeter dazukommt — und zwar erst beim Anlegen des
 * Schemas, nicht beim Lesen des Codes.
 */
class PostgisSetupTest extends KernelTestCase
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

    public function testDoctrineKnowsTheGeometryTypes(): void
    {
        self::assertTrue(Type::hasType('geometry'), 'Der Typ geometry ist registriert.');
        self::assertTrue(Type::hasType('geography'), 'Der Typ geography ist registriert.');
        self::assertInstanceOf(GeometryType::class, Type::getType('geometry'));
        self::assertInstanceOf(GeographyType::class, Type::getType('geography'));
    }

    /**
     * Der Filter ist der Grund, warum Doctrine die Tabellen von PostGIS nicht
     * fuer verwaist haelt. spatial_ref_sys liegt in public, mitten zwischen den
     * eigenen Tabellen — ohne den Filter stuende sie auf der Abschussliste.
     */
    public function testTheSchemaFilterSparesThePostgisTables(): void
    {
        $filter = $this->entityManager->getConnection()->getConfiguration()->getSchemaAssetsFilter();

        foreach (['spatial_ref_sys', 'tiger_data', 'topology'] as $fremd) {
            self::assertFalse((bool) $filter($fremd), sprintf('%s bleibt aussen vor.', $fremd));
        }

        foreach (['city', 'ride', 'app_user', 'track_polyline'] as $eigen) {
            self::assertTrue((bool) $filter($eigen), sprintf('%s wird weiterhin verwaltet.', $eigen));
        }
    }

    public function testThePostgisExtensionIsEnabled(): void
    {
        if (!$this->istPostgreSql()) {
            self::markTestSkipped('PostGIS gibt es nur fuer PostgreSQL.');
        }

        $version = $this->entityManager->getConnection()
            ->fetchOne("SELECT extversion FROM pg_extension WHERE extname = 'postgis'");

        self::assertNotFalse($version, 'Die Extension postgis ist eingeschaltet.');
        self::assertMatchesRegularExpression('/^3\./', (string) $version, 'PostGIS 3.x');
    }

    /**
     * Die eigentliche Probe: Eine Geometriespalte laesst sich anlegen, fuellen
     * und raeumlich abfragen — mit Doctrines eigener Verbindung.
     */
    public function testAGeometryColumnCanBeCreatedFilledAndQueried(): void
    {
        if (!$this->istPostgreSql()) {
            self::markTestSkipped('PostGIS gibt es nur fuer PostgreSQL.');
        }

        $verbindung = $this->entityManager->getConnection();
        $tabelle = 'geo_probe_' . substr(md5(uniqid('', true)), 0, 8);

        $verbindung->executeStatement(sprintf(
            'CREATE TABLE %s (id serial PRIMARY KEY, punkt geometry(POINT, 4326))', $tabelle
        ));

        try {
            $verbindung->executeStatement(sprintf(
                'CREATE INDEX %s_gist ON %s USING gist (punkt)', $tabelle, $tabelle
            ));

            // Hamburg
            $verbindung->executeStatement(sprintf(
                'INSERT INTO %s (punkt) VALUES (ST_SetSRID(ST_MakePoint(9.99, 53.55), 4326))', $tabelle
            ));

            self::assertSame('POINT(9.99 53.55)', $verbindung->fetchOne(
                sprintf('SELECT ST_AsText(punkt) FROM %s', $tabelle)
            ));

            // Entfernung nach Berlin, ueber geography also in Metern.
            $kilometer = (int) round((float) $verbindung->fetchOne(sprintf(
                'SELECT ST_Distance(punkt::geography, ST_SetSRID(ST_MakePoint(13.40, 52.52), 4326)::geography) / 1000
                 FROM %s', $tabelle
            )));

            self::assertGreaterThan(240, $kilometer, 'Hamburg und Berlin liegen rund 255 km auseinander.');
            self::assertLessThan(270, $kilometer);
        } finally {
            $verbindung->executeStatement(sprintf('DROP TABLE IF EXISTS %s', $tabelle));
        }
    }
}
