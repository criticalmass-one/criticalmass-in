<?php declare(strict_types=1);

namespace Tests\Repository;

use App\Entity\City;
use App\Entity\Ride;
use App\Repository\CityRepository;
use App\Repository\RideRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Die Suche schneidet bei 50 Treffern ab. Ohne Reihenfolge waeren das 50
 * beliebige — deshalb muss sie feststehen, und deaktivierte Touren gehoeren
 * gar nicht hinein.
 */
class SearchByQueryTest extends KernelTestCase
{
    private const SUCHWORT = 'Zwiebelkuchen';

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        $this->entityManager->createQuery('DELETE FROM App\\Entity\\Ride r WHERE r.city IN (SELECT c FROM App\\Entity\\City c WHERE c.city LIKE :muster)')
            ->setParameter('muster', 'Suchprobe %')
            ->execute();
        $this->entityManager->createQuery('DELETE FROM App\\Entity\\City c WHERE c.city LIKE :muster')
            ->setParameter('muster', 'Suchprobe %')
            ->execute();

        parent::tearDown();
    }

    private function stadt(string $name, string $titel, ?string $beschreibung = null, bool $aktiv = true): City
    {
        $stadt = new City();
        $stadt->setCity('Suchprobe ' . $name);
        $stadt->setTitle($titel);
        $stadt->setDescription($beschreibung);
        $stadt->setEnabled($aktiv);
        $stadt->setLatitude(53.55);
        $stadt->setLongitude(9.99);
        $this->entityManager->persist($stadt);
        $this->entityManager->flush();

        return $stadt;
    }

    private function tour(City $stadt, string $titel, string $abstand, bool $aktiv = true): Ride
    {
        $tour = new Ride();
        $tour->setCity($stadt);
        $tour->setTitle($titel);
        $tour->setDateTime(new \DateTime($abstand));
        $tour->setCreatedAt(new \DateTime());
        $tour->setUpdatedAt(new \DateTime());
        $tour->setEnabled($aktiv);
        $this->entityManager->persist($tour);
        $this->entityManager->flush();

        return $tour;
    }

    /**
     * @param array<int, Ride> $touren
     *
     * @return array<int, string>
     */
    private function titel(array $touren): array
    {
        return array_map(static fn (Ride $tour): string => (string) $tour->getTitle(), $touren);
    }

    public function testRideSearchLeavesOutDisabledRidesAndRidesOfDisabledCities(): void
    {
        $stadt = $this->stadt('Touren', 'Critical Mass Suchprobe');
        $aus = $this->stadt('Abgeschaltet', 'Critical Mass Abgeschaltet', null, false);

        $this->tour($stadt, 'Zwiebelkuchenfahrt aktiv', '+3 days');
        $this->tour($stadt, 'Zwiebelkuchenfahrt deaktiviert', '+5 days', false);
        $this->tour($aus, 'Zwiebelkuchenfahrt stillgelegte Stadt', '+4 days');

        $treffer = $this->titel(static::getContainer()->get(RideRepository::class)->searchByQuery(self::SUCHWORT));

        self::assertSame(['Zwiebelkuchenfahrt aktiv'], $treffer);
    }

    public function testRideSearchListsUpcomingRidesFirstThenPastOnes(): void
    {
        $stadt = $this->stadt('Touren', 'Critical Mass Suchprobe');

        $this->tour($stadt, 'Zwiebelkuchenfahrt vor zehn Tagen', '-10 days');
        $this->tour($stadt, 'Zwiebelkuchenfahrt in zehn Tagen', '+10 days');
        $this->tour($stadt, 'Zwiebelkuchenfahrt vor drei Tagen', '-3 days');
        $this->tour($stadt, 'Zwiebelkuchenfahrt in drei Tagen', '+3 days');

        $treffer = $this->titel(static::getContainer()->get(RideRepository::class)->searchByQuery(self::SUCHWORT));

        self::assertSame([
            'Zwiebelkuchenfahrt in drei Tagen',
            'Zwiebelkuchenfahrt in zehn Tagen',
            'Zwiebelkuchenfahrt vor drei Tagen',
            'Zwiebelkuchenfahrt vor zehn Tagen',
        ], $treffer);
    }

    public function testCitySearchRanksWordStartsFirstThenAlphabetically(): void
    {
        $this->stadt('B', 'Critical Mass Bebra', 'Bekannt fuer Zwiebelkuchen');
        $this->stadt('A', 'Critical Mass Grosszwiebelkuchenbach');
        $this->stadt('D', 'Critical Mass Zwiebelkuchen Nord');
        $this->stadt('C', 'Zwiebelkuchen Sued');

        $treffer = array_map(
            static fn (City $stadt): string => (string) $stadt->getCity(),
            static::getContainer()->get(CityRepository::class)->searchByQuery(self::SUCHWORT)
        );

        self::assertSame(['Suchprobe C', 'Suchprobe D', 'Suchprobe A', 'Suchprobe B'], $treffer);
    }
}
