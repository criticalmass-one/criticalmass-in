<?php declare(strict_types=1);

namespace Tests\Repository;

use App\Entity\City;
use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CityRepositoryTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager = null;
    private ?CityRepository $repository = null;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->repository = $this->entityManager->getRepository(City::class);
    }

    public function testFindActiveCitiesExcludesLowScoreCities(): void
    {
        $activeCities = $this->repository->findActiveCities();

        $cityNames = array_map(fn(City $city) => $city->getCity(), $activeCities);

        $this->assertNotContains('Ghosttown', $cityNames, 'Inactive city (score < 0.15) should be excluded');
    }

    public function testFindActiveCitiesIncludesHighScoreCities(): void
    {
        $activeCities = $this->repository->findActiveCities();

        $cityNames = array_map(fn(City $city) => $city->getCity(), $activeCities);

        $this->assertContains('Hamburg', $cityNames, 'Active city should be included');
        $this->assertContains('Berlin', $cityNames, 'Active city should be included');
        $this->assertContains('Munich', $cityNames, 'Active city should be included');
    }

    public function testFindActiveCitiesIncludesNullScoreCities(): void
    {
        $activeCities = $this->repository->findActiveCities();

        $cityNames = array_map(fn(City $city) => $city->getCity(), $activeCities);

        $this->assertContains('Kiel', $cityNames, 'City with NULL activity score should be included');
    }

    public function testFindEnabledCitiesIncludesAllEnabledCities(): void
    {
        $enabledCities = $this->repository->findEnabledCities();

        $cityNames = array_map(fn(City $city) => $city->getCity(), $enabledCities);

        $this->assertContains('Ghosttown', $cityNames, 'findEnabledCities should include inactive cities');
        $this->assertContains('Hamburg', $cityNames);
        $this->assertContains('Berlin', $cityNames);
    }

    public function testActivityScoreThreshold(): void
    {
        $this->assertEquals(0.15, CityRepository::ACTIVITY_SCORE_THRESHOLD);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager = null;
        $this->repository = null;
    }
}
