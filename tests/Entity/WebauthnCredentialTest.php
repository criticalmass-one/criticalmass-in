<?php declare(strict_types=1);

namespace Tests\Entity;

use App\Entity\User;
use App\Entity\WebauthnCredential;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use Webauthn\CredentialRecord;
use Webauthn\TrustPath\EmptyTrustPath;

class WebauthnCredentialTest extends TestCase
{
    public function testCreateFromRecordKeepsEveryCryptographicField(): void
    {
        $user = new User();
        $record = $this->createRecord();

        $credential = WebauthnCredential::createFromRecord($record, $user, 'Mein Telefon');

        self::assertSame($record->publicKeyCredentialId, $credential->publicKeyCredentialId);
        self::assertSame($record->type, $credential->type);
        self::assertSame($record->transports, $credential->transports);
        self::assertSame($record->attestationType, $credential->attestationType);
        self::assertSame($record->trustPath, $credential->trustPath);
        self::assertSame($record->aaguid, $credential->aaguid);
        self::assertSame($record->credentialPublicKey, $credential->credentialPublicKey);
        self::assertSame($record->userHandle, $credential->userHandle);
        self::assertSame($record->counter, $credential->counter);
        self::assertSame($record->backupEligible, $credential->backupEligible);
        self::assertSame($record->backupStatus, $credential->backupStatus);
        self::assertSame($record->uvInitialized, $credential->uvInitialized);
    }

    public function testCreateFromRecordAssignsUserAndName(): void
    {
        $user = new User();

        $credential = WebauthnCredential::createFromRecord($this->createRecord(), $user, 'Mein Telefon');

        self::assertSame($user, $credential->getUser());
        self::assertSame('Mein Telefon', $credential->getName());
        self::assertNull($credential->getLastUsedAt());
    }

    public function testPrePersistSetsCreationDate(): void
    {
        $credential = WebauthnCredential::createFromRecord($this->createRecord(), new User(), 'Passkey 1');

        self::assertNull($credential->getCreatedAt());

        $credential->prePersist();

        self::assertInstanceOf(\DateTime::class, $credential->getCreatedAt());
    }

    public function testMarkUsedAdvancesTheSignatureCounter(): void
    {
        $credential = WebauthnCredential::createFromRecord($this->createRecord(), new User(), 'Passkey 1');

        self::assertSame(23, $credential->counter);

        $credential->markUsed(24);

        self::assertSame(24, $credential->counter);
        self::assertInstanceOf(\DateTime::class, $credential->getLastUsedAt());
    }

    private function createRecord(): CredentialRecord
    {
        return CredentialRecord::create(
            'credential-id-raw',
            'public-key',
            ['internal', 'hybrid'],
            'none',
            EmptyTrustPath::create(),
            Uuid::fromString('00000000-0000-0000-0000-000000000000'),
            'public-key-bytes',
            'user-handle-uuid',
            23,
            null,
            true,
            true,
            true,
        );
    }
}
