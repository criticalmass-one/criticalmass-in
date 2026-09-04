<?php declare(strict_types=1);

namespace Tests\Repository;

use App\Entity\City;
use App\Entity\Ride;
use App\Entity\User;
use App\Repository\CityRepository;
use App\Repository\PhotoRepository;
use App\Repository\RideRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Abfragen mit GROUP BY muessen auch unter strenger Auslegung durchgehen.
 *
 * MySQL erlaubt es normalerweise, Spalten auszuwaehlen, die weder gruppiert noch
 * aggregiert sind, und sucht sich dann stillschweigend eine Zeile aus.
 * PostgreSQL lehnt das ab. Diese Tests fahren dieselben Abfragen, die die
 * Anwendung fahert — laeuft die Datenbank mit ONLY_FULL_GROUP_BY, gilt hier
 * dieselbe Strenge wie unter PostgreSQL.
 */
class GroupedQueryPortabilityTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    private function photoRepository(): PhotoRepository
    {
        $repository = static::getContainer()->get(PhotoRepository::class);
        self::assertInstanceOf(PhotoRepository::class, $repository);

        return $repository;
    }

    private function anyCity(): City
    {
        $city = $this->entityManager->getRepository(City::class)->findOneBy([]);
        self::assertInstanceOf(City::class, $city);

        return $city;
    }

    private function anyUser(): User
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([]);
        self::assertInstanceOf(User::class, $user);

        return $user;
    }

    public function testRidesWithPhotoCounterByUserRuns(): void
    {
        $rows = $this->photoRepository()->findRidesWithPhotoCounterByUser($this->anyUser());

        // Dass die Abfrage ueberhaupt durchlaeuft, ist hier die halbe Aussage:
        // unter ONLY_FULL_GROUP_BY wuerde die alte Fassung mit einer Ausnahme
        // abbrechen statt sich stillschweigend eine Zeile auszusuchen.
        $zeitpunkte = [];

        foreach ($rows as $row) {
            self::assertInstanceOf(Ride::class, $row[0], 'Die erste Spalte ist die Tour, nicht mehr ein beliebiges Foto.');
            self::assertGreaterThan(0, $row[1], 'Gezaehlt wird mindestens ein Foto je Zeile.');
            $zeitpunkte[] = $row[0]->getDateTime()->getTimestamp();
        }

        $absteigend = $zeitpunkte;
        rsort($absteigend);
        self::assertSame($absteigend, $zeitpunkte, 'Die Sortierung nach Datum ueberlebt das Nachladen der Touren.');
    }

    public function testRidesWithPhotoCounterRuns(): void
    {
        $result = $this->photoRepository()->findRidesWithPhotoCounter($this->anyCity());

        self::assertArrayHasKey('rides', $result);
        self::assertArrayHasKey('counter', $result);
        self::assertSame(array_keys($result['rides']), array_keys($result['counter']), 'Beide Teillisten sind gleich verschluesselt.');

        foreach ($result['rides'] as $key => $ride) {
            self::assertInstanceOf(Ride::class, $ride);
            self::assertGreaterThan(0, $result['counter'][$key]);
        }
    }

    public function testRidesWithPhotoCounterWithoutCityRuns(): void
    {
        $result = $this->photoRepository()->findRidesWithPhotoCounter();

        self::assertArrayHasKey('rides', $result);
        self::assertArrayHasKey('counter', $result);
    }

    public function testLocationsForCityReturnOneRowPerName(): void
    {
        $repository = static::getContainer()->get(RideRepository::class);
        self::assertInstanceOf(RideRepository::class, $repository);

        $rows = $repository->getLocationsForCity($this->anyCity());

        $namen = array_column($rows, 'location');
        self::assertSame(
            count($namen),
            count(array_unique($namen)),
            'Je Ortsname genau eine Zeile — sonst waere aus dem Mittelwert nichts geworden.'
        );

        foreach ($rows as $row) {
            self::assertArrayHasKey('latitude', $row);
            self::assertArrayHasKey('longitude', $row);
            if ($row['latitude'] !== null) {
                self::assertIsNumeric($row['latitude']);
                self::assertIsNumeric($row['longitude']);
            }
        }
    }

    /**
     * Die Abfrage muss von der Entity ausgehen, die sie liefert. Eine verbundene
     * Kennung ohne ihre Wurzel auszuwaehlen ist in DQL ein Fehler — genau daran
     * ist am 4. September die Feed-Aufwaermung gescheitert.
     */
    public function testGroupedQueriesGroupByIdentifiers(): void
    {
        $builder = $this->entityManager->createQueryBuilder()
            ->select('ride')
            ->addSelect('COUNT(photo.id)')
            ->from(Ride::class, 'ride')
            ->innerJoin('ride.photos', 'photo')
            ->groupBy('ride.id');

        $sql = $builder->getQuery()->getSQL();

        self::assertStringContainsString('GROUP BY', $sql);
        self::assertMatchesRegularExpression('/GROUP BY [a-z0-9_]+\.id/i', $sql, 'Gruppiert wird ueber den Primaerschluessel.');
    }

    public function testCitySearchStillWorksAlongsideTheGroupedQueries(): void
    {
        $repository = static::getContainer()->get(CityRepository::class);
        self::assertInstanceOf(CityRepository::class, $repository);

        $treffer = $repository->searchByQuery('Hamburg');
        $namen = array_map(static fn (City $city): string => (string) $city->getCity(), $treffer);

        self::assertContains('Hamburg', $namen);
    }
}
