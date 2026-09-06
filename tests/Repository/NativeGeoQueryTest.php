<?php declare(strict_types=1);

namespace Tests\Repository;

use App\Entity\City;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use App\Entity\Location;
use App\Entity\Ride;
use App\Repository\CityRepository;
use App\Repository\RideRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Die beiden Umkreissuchen laufen als handgeschriebenes SQL an DQL vorbei.
 *
 * Genau deshalb brauchen sie einen Test, der sie wirklich ausfuehrt: Ein Fehler
 * darin faellt sonst weder PHPStan noch der uebrigen Suite auf. Beim Umstieg auf
 * PostgreSQL ist mir hier ein Spaltenname in Anfuehrungszeichen geraten, den es
 * dort nicht gibt — unbemerkt, weil diese Abfragen von keinem Test beruehrt
 * wurden.
 *
 * Zum Spaltennamen: Doctrine legt "dateTime" unquotiert an, PostgreSQL faltet
 * ihn also auf "datetime". Dieselbe Faltung trifft die Abfrage, solange auch sie
 * nicht quotet — ein "dateTime" in Anfuehrungszeichen griffe dagegen ins Leere.
 */
class NativeGeoQueryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    private function rideRepository(): RideRepository
    {
        $repository = static::getContainer()->get(RideRepository::class);
        self::assertInstanceOf(RideRepository::class, $repository);

        return $repository;
    }

    private function cityRepository(): CityRepository
    {
        $repository = static::getContainer()->get(CityRepository::class);
        self::assertInstanceOf(CityRepository::class, $repository);

        return $repository;
    }

    private function hamburg(): City
    {
        $city = $this->entityManager->getRepository(City::class)->findOneBy(['city' => 'Hamburg']);
        self::assertInstanceOf(City::class, $city);

        return $city;
    }

    /**
     * Ein Ort auf der Startkoordinate einer vorhandenen Fahrt — so trifft die
     * Umkreissuche auch wirklich etwas.
     */
    private function ortAnEinerFahrt(): Location
    {
        $ride = $this->entityManager->getRepository(Ride::class)->findOneBy(['city' => $this->hamburg()]);
        self::assertInstanceOf(Ride::class, $ride);
        self::assertNotNull($ride->getLatitude(), 'Die Fahrt braucht Koordinaten, sonst prueft der Test nichts.');

        $location = (new Location())
            ->setCity($this->hamburg())
            ->setTitle('Testort ' . uniqid())
            ->setLatitude($ride->getLatitude())
            ->setLongitude($ride->getLongitude());

        $this->entityManager->persist($location);
        $this->entityManager->flush();

        return $location;
    }

    public function testRidesForLocationRunsAndFillsTheEntities(): void
    {
        $location = $this->ortAnEinerFahrt();

        $rides = $this->rideRepository()->findRidesForLocation($location, 5000.0, 10);

        self::assertNotEmpty($rides, 'Auf der eigenen Koordinate muss die Umkreissuche etwas finden.');

        foreach ($rides as $ride) {
            self::assertInstanceOf(Ride::class, $ride);
            // Das Datum kommt ueber einen Alias aus der Unterabfrage; ist der
            // Spaltenname falsch, bleibt es leer oder die Abfrage bricht ab.
            self::assertNotNull($ride->getDateTime(), 'Die Fahrt traegt ihr Datum.');
            self::assertNotNull($ride->getCity(), 'Die Stadt ist mitgeladen.');
        }

        $this->entityManager->remove($location);
        $this->entityManager->flush();
    }

    public function testRidesForLocationRespectsTheRadius(): void
    {
        $location = $this->ortAnEinerFahrt();

        $nah = $this->rideRepository()->findRidesForLocation($location, 100.0, 25);
        $weit = $this->rideRepository()->findRidesForLocation($location, 100000.0, 25);

        self::assertLessThanOrEqual(count($weit), count($nah), 'Ein groesserer Umkreis findet nicht weniger.');

        $this->entityManager->remove($location);
        $this->entityManager->flush();
    }

    public function testLocationWithoutCoordinatesReturnsNothing(): void
    {
        $location = (new Location())
            ->setCity($this->hamburg())
            ->setTitle('Ort ohne Koordinaten ' . uniqid());

        self::assertSame([], $this->rideRepository()->findRidesForLocation($location));
    }

    public function testNearCitiesRuns(): void
    {
        $treffer = $this->cityRepository()->findNearCities($this->hamburg(), 5, 500.0);

        foreach ($treffer as $city) {
            self::assertInstanceOf(City::class, $city);
            self::assertNotSame('Hamburg', $city->getCity(), 'Die Ausgangsstadt zaehlt nicht als Nachbarin.');
        }

        self::assertLessThanOrEqual(5, count($treffer));
    }

    /**
     * Der Nachweis, dass die Umstellung von der Haversine-Formel auf PostGIS
     * nichts verschoben hat (Issue #1141).
     *
     * Beide rechnen dieselbe Entfernung, nur auf unterschiedlichen Koerpern: die
     * Formel auf einer Kugel mit 6371 km Radius, ST_Distance ueber geography auf
     * dem WGS84-Ellipsoid. Der Unterschied liegt im Promillebereich — waere er
     * groesser, haette ich beim Umstellen etwas verwechselt, etwa Laenge und
     * Breite oder Meter und Kilometer.
     */
    public function testTheSpatialDistanceMatchesTheOldFormula(): void
    {
        if (!$this->entityManager->getConnection()->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            self::markTestSkipped('PostGIS-Funktionen gibt es nur unter PostgreSQL.');
        }

        $hamburg = $this->hamburg();

        $zeilen = $this->entityManager->getConnection()->fetchAllAssociative(
            'SELECT
                 c.city,
                 6371 * acos(LEAST(1, GREATEST(-1,
                     cos(radians(:lat)) * cos(radians(c.latitude))
                     * cos(radians(c.longitude) - radians(:lon))
                     + sin(radians(:lat)) * sin(radians(c.latitude))
                 ))) AS formel,
                 ST_Distance(c.coordinates::geography,
                             ST_SetSRID(ST_MakePoint(:lon, :lat), 4326)::geography) / 1000 AS postgis
             FROM city c
             WHERE c.coordinates IS NOT NULL AND c.id <> :id',
            ['lat' => $hamburg->getLatitude(), 'lon' => $hamburg->getLongitude(), 'id' => $hamburg->getId()]
        );

        self::assertNotEmpty($zeilen, 'Ohne Vergleichsstaedte sagt der Test nichts.');

        foreach ($zeilen as $zeile) {
            $formel = (float) $zeile['formel'];
            $postgis = (float) $zeile['postgis'];

            if ($formel < 1.0) {
                continue;
            }

            $abweichung = abs($formel - $postgis) / $formel;

            self::assertLessThan(0.01, $abweichung, sprintf(
                'Fuer %s: Formel %.1f km, PostGIS %.1f km — das ist mehr als ein Prozent auseinander.',
                $zeile['city'], $formel, $postgis
            ));
        }
    }

    /**
     * Und der Gegentest zum Radius: Was innerhalb liegt, muss gefunden werden,
     * was ausserhalb liegt, nicht.
     */
    public function testTheRadiusBoundaryHolds(): void
    {
        if (!$this->entityManager->getConnection()->getDatabasePlatform() instanceof PostgreSQLPlatform) {
            self::markTestSkipped('PostGIS-Funktionen gibt es nur unter PostgreSQL.');
        }

        $hamburg = $this->hamburg();

        $eng = $this->cityRepository()->findNearCities($hamburg, 50, 1.0);
        $weit = $this->cityRepository()->findNearCities($hamburg, 50, 5000.0);

        self::assertLessThanOrEqual(count($weit), count($eng), 'Ein groesserer Radius findet nicht weniger.');

        foreach ($eng as $nah) {
            self::assertContains($nah, $weit, 'Was im engen Radius liegt, liegt auch im weiten.');
        }
    }

    public function testNearCitiesOnlyReturnsEnabledCities(): void
    {
        foreach ($this->cityRepository()->findNearCities($this->hamburg(), 15, 2000.0) as $city) {
            self::assertTrue($city->getEnabled(), 'Abgeschaltete Staedte bleiben aussen vor.');
        }
    }
}
