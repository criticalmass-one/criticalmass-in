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

        $this->assertNotContains('Ghosttown', $cityNames, 'Inactive city (score below the threshold) should be excluded');
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

    /**
     * Eine neu angelegte Stadt hat noch keine Signale und damit Score 0 —
     * sie darf deshalb nicht sofort verschwinden.
     */
    public function testFindActiveCitiesKeepsNewCitiesWithoutSignals(): void
    {
        $city = new City();
        $city->setCity('Neugruendung');
        $city->setTitle('Critical Mass Neugruendung');
        $city->setEnabled(true);
        $city->setTimezone('Europe/Berlin');
        $city->setActivityScore(0.0);

        $this->entityManager->persist($city);
        $this->entityManager->flush();

        try {
            $cityNames = array_map(fn(City $city) => $city->getCity(), $this->repository->findActiveCities());

            $this->assertContains('Neugruendung', $cityNames, 'A city younger than the grace period counts as active');
            $this->assertNotContains('Ghosttown', $cityNames);
        } finally {
            $this->entityManager->remove($city);
            $this->entityManager->flush();
        }
    }

    public function testFindPopularCitiesSkipsInactiveCities(): void
    {
        $cityNames = array_map(fn(City $city) => $city->getCity(), $this->repository->findPopularCities());

        $this->assertNotContains('Ghosttown', $cityNames, 'The footer must not promote inactive cities');
        $this->assertContains('Hamburg', $cityNames);
        $this->assertContains('Kiel', $cityNames, 'An unscored city stays in the footer');
    }

    public function testActivityScoreThreshold(): void
    {
        $this->assertEquals(0.01, CityRepository::ACTIVITY_SCORE_THRESHOLD);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager = null;
        $this->repository = null;
    }
}
