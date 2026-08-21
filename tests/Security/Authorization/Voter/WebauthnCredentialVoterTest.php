<?php declare(strict_types=1);

namespace Tests\Security\Authorization\Voter;

use App\Entity\User;
use App\Entity\WebauthnCredential;
use App\Security\Authorization\Voter\WebauthnCredentialVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Uid\Uuid;
use Webauthn\CredentialRecord;
use Webauthn\TrustPath\EmptyTrustPath;

class WebauthnCredentialVoterTest extends TestCase
{
    public function testOwnerMayRenameAndDelete(): void
    {
        $owner = new User();
        $credential = $this->createCredential($owner);

        self::assertSame(VoterInterface::ACCESS_GRANTED, $this->vote('edit', $credential, $owner));
        self::assertSame(VoterInterface::ACCESS_GRANTED, $this->vote('delete', $credential, $owner));
    }

    public function testStrangerMayNotTouchAForeignPasskey(): void
    {
        $credential = $this->createCredential(new User());

        self::assertSame(VoterInterface::ACCESS_DENIED, $this->vote('edit', $credential, new User()));
        self::assertSame(VoterInterface::ACCESS_DENIED, $this->vote('delete', $credential, new User()));
    }

    /**
     * Anders als bei Tracks und Fotos gibt es hier keine Ausnahme für Administratoren:
     * Wer fremde Passkeys löschen könnte, könnte Nutzer aus ihren Konten aussperren, und
     * wer sie anlegen könnte, käme in fremde Konten hinein.
     */
    public function testAdministratorsHaveNoSpecialAccess(): void
    {
        $credential = $this->createCredential(new User());

        $administrator = new User();
        $administrator->setRoles(['ROLE_ADMIN']);

        self::assertSame(VoterInterface::ACCESS_DENIED, $this->vote('edit', $credential, $administrator));
        self::assertSame(VoterInterface::ACCESS_DENIED, $this->vote('delete', $credential, $administrator));
    }

    public function testVoterAbstainsFromUnrelatedSubjects(): void
    {
        self::assertSame(VoterInterface::ACCESS_ABSTAIN, $this->vote('edit', new User(), new User()));
    }

    private function vote(string $attribute, object $subject, User $user): int
    {
        $token = $this->createMock(TokenInterface::class);
        $token
            ->method('getUser')
            ->willReturn($user);

        return (new WebauthnCredentialVoter())->vote($token, $subject, [$attribute]);
    }

    private function createCredential(User $user): WebauthnCredential
    {
        $record = CredentialRecord::create(
            'credential-id-raw',
            'public-key',
            ['internal'],
            'none',
            EmptyTrustPath::create(),
            Uuid::fromString('00000000-0000-0000-0000-000000000000'),
            'public-key-bytes',
            'user-handle-uuid',
            0,
        );

        return WebauthnCredential::createFromRecord($record, $user, 'Passkey 1');
    }
}
