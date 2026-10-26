<?php declare(strict_types=1);

namespace Tests\Repository;

use App\Entity\WebauthnCredential;
use App\Repository\WebauthnCredentialRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Webauthn\PublicKeyCredentialUserEntity;

class WebauthnCredentialRepositoryTest extends TestCase
{
    /**
     * Die Credential-ID kommt roh aus der WebAuthn-Bibliothek und geht roh an
     * findOneBy(); das Base64-Kodieren macht der DBAL-Typ des Feldes. Dass das gegen
     * die echte Datenbank stimmt, prueft WebauthnCredentialLookupTest.
     */
    public function testCredentialLookupPassesTheRawId(): void
    {
        $credential = $this->createMock(WebauthnCredential::class);

        $repository = $this->createRepositoryMock();
        $repository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['publicKeyCredentialId' => 'rohe-credential-id'])
            ->willReturn($credential);

        self::assertSame($credential, $repository->findOneByCredentialId('rohe-credential-id'));
    }

    public function testCredentialsAreLookedUpByUserHandle(): void
    {
        $userEntity = PublicKeyCredentialUserEntity::create(
            'radfahrerin@example.org',
            '11111111-2222-3333-4444-555555555555',
            'radfahrerin',
        );

        $repository = $this->createRepositoryMock();
        $repository
            ->expects(self::once())
            ->method('findBy')
            ->with(['userHandle' => '11111111-2222-3333-4444-555555555555'])
            ->willReturn([]);

        self::assertSame([], $repository->findAllForUserEntity($userEntity));
    }

    /**
     * @return WebauthnCredentialRepository&MockObject
     */
    private function createRepositoryMock(): WebauthnCredentialRepository
    {
        return $this->getMockBuilder(WebauthnCredentialRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['findOneBy', 'findBy'])
            ->getMock();
    }
}
