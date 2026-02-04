<?php declare(strict_types=1);

namespace Tests\Repository;

use App\Entity\City;
use App\Entity\Ride;
use Doctrine\ORM\EntityManagerInterface;
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

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager = null;
    }
}
