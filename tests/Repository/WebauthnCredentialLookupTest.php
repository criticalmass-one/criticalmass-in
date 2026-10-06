<?php declare(strict_types=1);

namespace Tests\Repository;

use App\Entity\User;
use App\Entity\WebauthnCredential;
use App\Repository\WebauthnCredentialRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;
use Webauthn\CredentialRecord;
use Webauthn\TrustPath\EmptyTrustPath;

/**
 * Speichern und Wiederfinden eines Passkeys gegen die echte Datenbank.
 *
 * Ein Mock von findOneBy() kann nicht zeigen, wie Doctrine den Suchwert ueber den
 * DBAL-Typ `base64` umrechnet. Genau daran ist die Anmeldung gescheitert: Die
 * Credential-ID wurde von Hand und vom Typ kodiert, also doppelt.
 */
class WebauthnCredentialLookupTest extends KernelTestCase
{
    public function testASavedPasskeyIsFoundByItsRawCredentialId(): void
    {
        self::bootKernel();
        $em = static::getContainer()->get('doctrine')->getManager();

        $user = $em->getRepository(User::class)->findOneBy(['email' => 'cyclist@criticalmass.in']);
        $handle = $user->getWebauthnUserHandle() ?? (string) Uuid::v4();
        $user->setWebauthnUserHandle($handle);
        $em->flush();

        // Rohe Bytes, wie sie ein Authenticator liefert — inklusive Zeichen, die erst
        // durch die Kodierung druckbar werden.
        $rawId = random_bytes(32);

        $record = CredentialRecord::create(
            $rawId,
            'public-key',
            ['internal'],
            'none',
            EmptyTrustPath::create(),
            Uuid::fromString('00000000-0000-0000-0000-000000000000'),
            random_bytes(77),
            $handle,
            0,
        );

        /** @var WebauthnCredentialRepository $repository */
        $repository = $em->getRepository(WebauthnCredential::class);
        $repository->saveCredentialRecord($record);
        $em->clear();

        $found = $repository->findOneByCredentialId($rawId);

        self::assertInstanceOf(WebauthnCredential::class, $found);
        self::assertSame($rawId, $found->publicKeyCredentialId);
        self::assertSame($user->getId(), $found->getUser()->getId());

        self::assertNull($repository->findOneByCredentialId(random_bytes(32)));
        self::assertNull($found->getLastUsedAt(), 'Frisch angelegt ist er noch nicht benutzt.');

        // So kommt er nach einer Anmeldung zurueck: das geladene Entity, Zaehler erhoeht.
        $found->counter = 1;
        $repository->saveCredentialRecord($found);
        $em->clear();

        $used = $repository->findOneByCredentialId($rawId);
        self::assertNotNull($used->getLastUsedAt(), 'Nach der Anmeldung muss „Zuletzt benutzt“ gesetzt sein.');
        self::assertSame(1, $used->counter);
    }
}
