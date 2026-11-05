<?php declare(strict_types=1);

namespace Tests\Criticalmass\DataQuery\Query;

use App\Criticalmass\DataQuery\Query\RegionQuery;
use App\Entity\City;
use App\Entity\Region;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Staedte haengen in den Fixtures am Bundesland. Die Karte einer Laender-,
 * Kontinent- oder Weltseite fragt aber mit deren Slug ab.
 */
class RegionQueryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
    }

    /**
     * @return array<int, string>
     */
    private function cityNamesIn(string $regionSlug): array
    {
        $region = $this->entityManager->getRepository(Region::class)->findOneBy(['slug' => $regionSlug]);
        self::assertNotNull($region, sprintf('Region %s fehlt in den Fixtures.', $regionSlug));

        $queryBuilder = $this->entityManager->getRepository(City::class)->createQueryBuilder('e');
        (new RegionQuery())->setRegion($region)->createOrmQuery($queryBuilder);

        return array_map(static fn (City $city): string => $city->getCity(), $queryBuilder->getQuery()->getResult());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function parentRegions(): array
    {
        return [
            'Land' => ['germany'],
            'Kontinent' => ['europe'],
            'Welt' => ['world'],
        ];
    }

    #[DataProvider('parentRegions')]
    public function testCitiesOfSubregionsAreIncluded(string $regionSlug): void
    {
        $names = $this->cityNamesIn($regionSlug);

        self::assertContains('Hamburg', $names);
        self::assertContains('Kiel', $names);
    }

    public function testAStateOnlyContainsItsOwnCities(): void
    {
        $names = $this->cityNamesIn('schleswig-holstein');

        self::assertContains('Kiel', $names);
        self::assertNotContains('Hamburg', $names);
    }
}
