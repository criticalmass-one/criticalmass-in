<?php declare(strict_types=1);

namespace Tests\Repository;

use App\Entity\City;
use App\Entity\SocialNetworkProfile;
use App\Repository\SocialNetworkProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\TestDox;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[TestDox('SocialNetworkProfileRepository')]
class SocialNetworkProfileRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private SocialNetworkProfileRepository $repository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $repository = $this->entityManager->getRepository(SocialNetworkProfile::class);
        \assert($repository instanceof SocialNetworkProfileRepository);

        $this->repository = $repository;
    }

    #[TestDox('returns only the cities that have a profile registered with the Feeds API')]
    public function testFindCitiesWithFeedsProfiles(): void
    {
        $cityNames = array_map(
            fn(City $city): ?string => $city->getCity(),
            $this->repository->findCitiesWithFeedsProfiles(),
        );

        $this->assertContains('Hamburg', $cityNames);
        $this->assertContains('Berlin', $cityNames);

        // Munich's profile carries no feeds id, so there is nothing to warm.
        $this->assertNotContains('Munich', $cityNames);
    }

    #[TestDox('lists every city once, however many profiles it has')]
    public function testFindCitiesWithFeedsProfilesHasNoDuplicates(): void
    {
        $cityNames = array_map(
            fn(City $city): ?string => $city->getCity(),
            $this->repository->findCitiesWithFeedsProfiles(),
        );

        // Hamburg has three profiles with a feeds id.
        $this->assertSame(array_unique($cityNames), $cityNames);
    }

    #[TestDox('maps feeds profile ids back to their network identifier')]
    public function testFindNetworkIdentifiersByFeedsProfileIds(): void
    {
        $networkIdentifiers = $this->repository->findNetworkIdentifiersByFeedsProfileIds([9001, 9003]);

        $this->assertSame([9001 => 'twitter', 9003 => 'instagram_profile'], $networkIdentifiers);
    }

    #[TestDox('asks nothing of the database for an empty id list')]
    public function testFindNetworkIdentifiersWithoutIds(): void
    {
        $this->assertSame([], $this->repository->findNetworkIdentifiersByFeedsProfileIds([]));
    }
}
