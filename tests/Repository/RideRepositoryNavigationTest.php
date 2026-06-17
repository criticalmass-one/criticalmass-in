<?php declare(strict_types=1);

namespace Tests\Repository;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Ride;
use App\Repository\RideRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Regression für #1353: Vor-/Zurück-Navigation muss innerhalb DERSELBEN Stadt
 * bleiben (nicht stadtübergreifend springen).
 */
final class RideRepositoryNavigationTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private RideRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = self::getContainer()->get('doctrine')->getManager();
        $this->repository = self::getContainer()->get(RideRepository::class);
        $this->em->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $connection = $this->em->getConnection();
        if ($connection->isTransactionActive()) {
            $connection->rollBack();
        }
        parent::tearDown();
    }

    private function city(string $name): City
    {
        $city = new City();
        $city->setCity($name);
        $city->setTitle('Critical Mass ' . $name);
        $city->setCreatedAt(new \DateTime());
        $this->em->persist($city);

        $slug = new CitySlug();
        $slug->setSlug('nav-' . substr(md5(uniqid('', true)), 0, 10));
        $slug->setCity($city);
        $this->em->persist($slug);
        $city->setMainSlug($slug);

        return $city;
    }

    private function ride(City $city, string $date): Ride
    {
        $ride = new Ride();
        $ride->setCity($city);
        $ride->setDateTime(new \DateTime($date));
        $ride->setTitle('Ride ' . $date);
        $this->em->persist($ride);

        return $ride;
    }

    public function testNavigationStaysWithinSameCity(): void
    {
        $cityA = $this->city('Stadt A');
        $cityB = $this->city('Stadt B');

        $a1 = $this->ride($cityA, '2026-03-01 19:00:00');
        $a2 = $this->ride($cityA, '2026-04-01 19:00:00');
        $a3 = $this->ride($cityA, '2026-05-01 19:00:00');
        // Stadt B hat einen Ride zwischen und nach den A-Rides — darf NICHT auftauchen.
        $this->ride($cityB, '2026-04-15 19:00:00');
        $this->ride($cityB, '2026-09-01 19:00:00');

        $this->em->flush();

        self::assertSame($a1->getId(), $this->repository->getPreviousRide($a2)?->getId());
        self::assertSame($a3->getId(), $this->repository->getNextRide($a2)?->getId());
    }

    public function testNoNeighbourAcrossCityBoundary(): void
    {
        $cityA = $this->city('Stadt A');
        $cityB = $this->city('Stadt B');

        $a1 = $this->ride($cityA, '2026-03-01 19:00:00');
        // Späterer Ride NUR in Stadt B → getNextRide für den letzten A-Ride muss null sein.
        $this->ride($cityB, '2026-12-01 19:00:00');

        $this->em->flush();

        self::assertNull($this->repository->getNextRide($a1), 'Kein nächster Ride in derselben Stadt');
        self::assertNull($this->repository->getPreviousRide($a1), 'Kein vorheriger Ride in derselben Stadt');
    }
}
