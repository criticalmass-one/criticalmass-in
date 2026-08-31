<?php declare(strict_types=1);

namespace Tests\Security\Webauthn;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\Webauthn\UserEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class UserEntityRepositoryTest extends TestCase
{
    public function testFindOneByUsernameUsesEmailAsNameAndHandleAsId(): void
    {
        $user = (new User())
            ->setEmail('radfahrerin@example.org')
            ->setUsername('radfahrerin')
            ->setWebauthnUserHandle('11111111-2222-3333-4444-555555555555');

        $repository = $this->createRepository($user, expectedFlushes: 0);

        $userEntity = $repository->findOneByUsername('radfahrerin@example.org');

        self::assertNotNull($userEntity);
        self::assertSame('radfahrerin@example.org', $userEntity->name);
        self::assertSame('11111111-2222-3333-4444-555555555555', $userEntity->id);
        self::assertSame('radfahrerin', $userEntity->displayName);
    }

    public function testFindOneByUsernameReturnsNullForUnknownAddress(): void
    {
        $repository = $this->createRepository(null, expectedFlushes: 0);

        self::assertNull($repository->findOneByUsername('niemand@example.org'));
    }

    public function testFindOneByUserHandleReturnsNullForUnknownHandle(): void
    {
        $repository = $this->createRepository(null, expectedFlushes: 0);

        self::assertNull($repository->findOneByUserHandle('unbekannt'));
    }

    /**
     * Bestandskonten haben noch keinen Handle. Er darf nicht aus der E-Mail-Adresse
     * abgeleitet werden, weil die sich im Profil jederzeit ändern lässt.
     */
    public function testHandleIsGeneratedOnFirstContactAndPersisted(): void
    {
        $user = (new User())
            ->setEmail('radfahrerin@example.org')
            ->setUsername('radfahrerin');

        $repository = $this->createRepository($user, expectedFlushes: 1);

        $userEntity = $repository->findOneByUsername('radfahrerin@example.org');

        self::assertNotNull($userEntity);
        self::assertNotSame('radfahrerin@example.org', $userEntity->id);
        self::assertTrue(Uuid::isValid($userEntity->id));
        self::assertSame($userEntity->id, $user->getWebauthnUserHandle());
    }

    public function testExistingHandleIsNeverRegenerated(): void
    {
        $user = (new User())
            ->setEmail('radfahrerin@example.org')
            ->setUsername('radfahrerin')
            ->setWebauthnUserHandle('11111111-2222-3333-4444-555555555555');

        $repository = $this->createRepository($user, expectedFlushes: 0);

        self::assertSame('11111111-2222-3333-4444-555555555555', $repository->resolveUserHandle($user));
        self::assertSame('11111111-2222-3333-4444-555555555555', $repository->resolveUserHandle($user));
    }

    /**
     * Der User-Handle darf höchstens 64 Byte lang sein, sonst weist der Authenticator ihn ab.
     */
    public function testGeneratedHandleStaysWithinTheWebauthnSizeLimit(): void
    {
        $user = (new User())->setEmail('radfahrerin@example.org')->setUsername('radfahrerin');

        $repository = $this->createRepository($user, expectedFlushes: 1);

        self::assertLessThanOrEqual(64, strlen($repository->resolveUserHandle($user)));
    }

    private function createRepository(?User $user, int $expectedFlushes): UserEntityRepository
    {
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository
            ->method('findOneBy')
            ->willReturn($user);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager
            ->expects(self::exactly($expectedFlushes))
            ->method('flush');

        $managerRegistry = $this->createMock(ManagerRegistry::class);
        $managerRegistry
            ->method('getManager')
            ->willReturn($entityManager);

        return new UserEntityRepository($userRepository, $managerRegistry);
    }
}
