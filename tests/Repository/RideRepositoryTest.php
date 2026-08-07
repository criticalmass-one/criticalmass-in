<?php declare(strict_types=1);

namespace Tests\Repository;

use App\Entity\City;
use App\Entity\Ride;
use App\Repository\RideRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bridge\PhpUnit\ClockMock;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RideRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager = null;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testFindFrontpageRidesExcludesInactiveCityRides(): void
    {
        $inactiveCity = $this->entityManager->getRepository(City::class)->findOneBy(['city' => 'Ghosttown']);

        if (!$inactiveCity) {
            $this->markTestSkipped('Inactive city fixture not found');
        }

        $this->assertLessThan(0.15, $inactiveCity->getActivityScore(), 'Test requires inactive city with low score');

        $rides = $this->entityManager->getRepository(Ride::class)->findFrontpageRides();

        foreach ($rides as $ride) {
            $this->assertNotEquals(
                $inactiveCity->getId(),
                $ride->getCity()->getId(),
                'Frontpage rides should not include rides from inactive cities'
            );
        }
    }

    public function testFindFrontpageRidesIncludesActiveCityRides(): void
    {
        $rides = $this->entityManager->getRepository(Ride::class)->findFrontpageRides();

        if (empty($rides)) {
            $this->markTestSkipped('No frontpage rides in test date range');
        }

        foreach ($rides as $ride) {
            $city = $ride->getCity();
            $this->assertTrue(
                $city->getActivityScore() === null || $city->getActivityScore() >= 0.15,
                sprintf('Ride from city %s should have activity score >= 0.15 or NULL', $city->getCity())
            );
        }
    }

    #[Group('time-sensitive')]
    public function testFindCurrentRideForCityIncludesRecentlyStartedRide(): void
    {
        [$city, $lastPastRide] = $this->getCityWithLastPastRide();

        // two hours after the start the ride still counts as current
        ClockMock::register(RideRepository::class);
        ClockMock::withClockMock((clone $lastPastRide->getDateTime())->modify('+2 hours')->format('U'));

        $currentRide = $this->entityManager->getRepository(Ride::class)->findCurrentRideForCity($city);

        $this->assertNotNull($currentRide);
        $this->assertEquals($lastPastRide->getId(), $currentRide->getId());
    }

    #[Group('time-sensitive')]
    public function testFindCurrentRideForCityExcludesRideAfterGracePeriod(): void
    {
        [$city, $lastPastRide] = $this->getCityWithLastPastRide();

        // one minute past the grace period the ride is not current anymore
        ClockMock::register(RideRepository::class);
        ClockMock::withClockMock(
            (clone $lastPastRide->getDateTime())
                ->modify(sprintf('+%d hours +1 minute', RideRepository::CURRENT_RIDE_GRACE_HOURS))
                ->format('U')
        );

        $currentRide = $this->entityManager->getRepository(Ride::class)->findCurrentRideForCity($city);

        if ($currentRide) {
            $this->assertNotEquals($lastPastRide->getId(), $currentRide->getId());
            $this->assertGreaterThan($lastPastRide->getDateTime(), $currentRide->getDateTime());
        } else {
            $this->assertNull($currentRide);
        }
    }

    /** @return array{City, Ride} */
    private function getCityWithLastPastRide(): array
    {
        $city = $this->entityManager->getRepository(City::class)->findOneBy(['city' => 'Hamburg']);
        $this->assertNotNull($city, 'Hamburg fixture not found');

        $lastPastRide = null;
        $now = new \DateTime();

        foreach ($this->entityManager->getRepository(Ride::class)->findRidesForCity($city, 'DESC') as $ride) {
            if ($ride->getDateTime() < $now) {
                $lastPastRide = $ride;
                break;
            }
        }

        $this->assertNotNull($lastPastRide, 'No past Hamburg ride in fixtures');

        return [$city, $lastPastRide];
    }

    protected function tearDown(): void
    {
        ClockMock::withClockMock(false);

        parent::tearDown();
        $this->entityManager = null;
    }
}
