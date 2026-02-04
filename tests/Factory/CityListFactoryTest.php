<?php declare(strict_types=1);

namespace Tests\Factory;

use App\Entity\City;
use App\Model\CityListModel;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CityListFactoryTest extends KernelTestCase
{
    public function testCityListUsesActiveCitiesRepository(): void
    {
        self::bootKernel();

        $doctrine = static::getContainer()->get(ManagerRegistry::class);
        $cityRepository = $doctrine->getRepository(City::class);

        $activeCities = $cityRepository->findActiveCities();
        $cityNames = array_map(fn(City $city) => $city->getCity(), $activeCities);

        $this->assertNotContains('Ghosttown', $cityNames, 'Inactive city should not appear in active cities');
        $this->assertContains('Hamburg', $cityNames);
        $this->assertContains('Berlin', $cityNames);
        $this->assertContains('Munich', $cityNames);
        $this->assertContains('Kiel', $cityNames, 'City with NULL activity score should be included');
    }
}
