<?php declare(strict_types=1);

namespace Tests\DQL;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Die eigenen DQL-Funktionen muessen auf MySQL und auf PostgreSQL gueltiges SQL
 * erzeugen.
 *
 * Beide Zweige lassen sich hier wirklich pruefen, ohne dass ein PostgreSQL
 * laeuft: DBAL bestimmt die Plattform aus der angegebenen serverVersion, und
 * getSQL() erzeugt die Anweisung, ohne sie auszufuehren.
 */
class PortableFunctionsTest extends KernelTestCase
{
    private EntityManagerInterface $mysql;
    private EntityManagerInterface $postgres;

    protected function setUp(): void
    {
        self::bootKernel();

        // Beide Manager werden ausdruecklich aufgebaut, statt den vorhandenen
        // fuer MySQL zu halten: Laeuft die Testsuite selbst schon gegen
        // PostgreSQL, waere diese Annahme falsch und der Test pruefte zweimal
        // dieselbe Plattform.
        $konfiguration = static::getContainer()->get('doctrine')->getManager()->getConfiguration();

        $this->mysql = $this->managerFor('pdo_mysql', 'mariadb-10.9.3', $konfiguration);
        $this->postgres = $this->managerFor('pdo_pgsql', '17', $konfiguration);
    }

    /**
     * Der Manager baut nie eine Verbindung auf: DBAL bestimmt die Plattform aus
     * der serverVersion, und getSQL() erzeugt die Anweisung, ohne sie
     * auszufuehren.
     */
    private function managerFor(string $treiber, string $version, Configuration $konfiguration): EntityManagerInterface
    {
        return new EntityManager(
            DriverManager::getConnection([
                'driver' => $treiber,
                'serverVersion' => $version,
                'host' => 'localhost',
                'dbname' => 'unbenutzt',
                'user' => 'unbenutzt',
                'password' => 'unbenutzt',
            ]),
            $konfiguration
        );
    }

    private function sql(EntityManagerInterface $manager, string $dql): string
    {
        return $manager->createQuery($dql)->getSQL();
    }

    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function standardFunktionen(): array
    {
        return [
            'YEAR'  => ['SELECT r FROM App\Entity\Ride r WHERE YEAR(r.dateTime) = 2026', 'EXTRACT(YEAR FROM'],
            'MONTH' => ['SELECT r FROM App\Entity\Ride r WHERE MONTH(r.dateTime) = 9', 'EXTRACT(MONTH FROM'],
            'DAY'   => ['SELECT r FROM App\Entity\Ride r WHERE DAY(r.dateTime) = 4', 'EXTRACT(DAY FROM'],
            'DATE'  => ['SELECT r FROM App\Entity\Ride r WHERE DATE(r.dateTime) = CURRENT_DATE()', 'CAST('],
        ];
    }

    /**
     * Vier der sechs Funktionen brauchen gar keine Fallunterscheidung: EXTRACT
     * und CAST gehoeren zum SQL-Standard und gelten auf beiden Plattformen
     * gleichermassen.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('standardFunktionen')]
    public function testStandardFunctionsAreIdenticalOnBothPlatforms(string $dql, string $erwartet): void
    {
        $aufMysql = $this->sql($this->mysql, $dql);
        $aufPostgres = $this->sql($this->postgres, $dql);

        self::assertStringContainsString($erwartet, $aufMysql);
        self::assertStringContainsString($erwartet, $aufPostgres);
    }

    public function testRandUsesTheNameEachPlatformKnows(): void
    {
        $dql = 'SELECT r FROM App\Entity\Ride r ORDER BY RAND() ASC';

        self::assertStringContainsString('RAND()', $this->sql($this->mysql, $dql));

        $aufPostgres = $this->sql($this->postgres, $dql);
        self::assertStringContainsString('RANDOM()', $aufPostgres);
        self::assertStringNotContainsString('RAND()', $aufPostgres);
    }

    /**
     * Hier weicht nicht nur der Name ab, sondern die Zaehlung: MySQL zaehlt den
     * Sonntag als 1, PostgreSQL als 0. Das +1 gleicht das aus — fehlte es,
     * waere jeder Wochentag um eins verschoben, ohne dass etwas auffiele.
     */
    public function testDayOfWeekKeepsTheSameNumbering(): void
    {
        $dql = 'SELECT r FROM App\Entity\Ride r WHERE DAYOFWEEK(r.dateTime) = 1';

        self::assertStringContainsString('DAYOFWEEK(', $this->sql($this->mysql, $dql));

        $aufPostgres = $this->sql($this->postgres, $dql);
        self::assertStringContainsString('EXTRACT(DOW FROM', $aufPostgres);
        self::assertStringContainsString('+ 1', $aufPostgres);
    }

    /**
     * Die Mathematikfunktionen aus beberlei/doctrineextensions liegen im
     * Namensraum \Mysql\, erzeugen aber Standard-SQL. Issue #1135 verlangt,
     * sie zu entfernen — fuer die Portabilitaet ist das nicht noetig.
     */
    public function testBorrowedMathFunctionsAreAlreadyPortable(): void
    {
        $dql = 'SELECT r FROM App\Entity\Ride r WHERE COS(RADIANS(r.latitude)) > 0';

        foreach ([$this->mysql, $this->postgres] as $manager) {
            $sql = $this->sql($manager, $dql);
            self::assertStringContainsString('COS(', $sql);
            self::assertStringContainsString('RADIANS(', $sql);
        }
    }

    public function testTheRepositoryQueriesThatUseThemStillRun(): void
    {
        $repository = static::getContainer()->get(\App\Repository\RideRepository::class);
        self::assertInstanceOf(\App\Repository\RideRepository::class, $repository);

        // findRecentRides filtert ueber MONTH() und YEAR().
        $rides = $repository->findRecentRides(2026, 9, 5);

        self::assertLessThanOrEqual(5, count($rides));
    }
}
