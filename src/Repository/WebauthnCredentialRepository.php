<?php declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Entity\WebauthnCredential;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Webauthn\Bundle\Repository\CanSaveCredentialRecord;
use Webauthn\Bundle\Repository\CredentialRecordRepositoryInterface;
use Webauthn\Bundle\Repository\PublicKeyCredentialSourceRepositoryInterface;
use Webauthn\CredentialRecord;
use Webauthn\PublicKeyCredentialUserEntity;

/**
 * Ablage der registrierten Passkeys.
 *
 * Bewusst ein eigenes Repository statt der mitgelieferten
 * `Webauthn\Bundle\Repository\DoctrineCredentialSourceRepository`: die ist seit 5.2
 * deprecated und verschwindet in 6.0.
 *
 * PublicKeyCredentialSourceRepositoryInterface ist ein leeres Marker-Interface, das
 * seinerseits von CredentialRecordRepositoryInterface erbt und ebenfalls in 6.0 entfällt.
 * Es steht hier nur, weil das Bundle den DI-Alias darauf setzt und der Container sonst
 * nicht baut; Methoden bringt es keine mit.
 *
 * @extends ServiceEntityRepository<WebauthnCredential>
 */
class WebauthnCredentialRepository extends ServiceEntityRepository implements CredentialRecordRepositoryInterface, PublicKeyCredentialSourceRepositoryInterface, CanSaveCredentialRecord
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly UserRepository $userRepository,
    ) {
        parent::__construct($registry, WebauthnCredential::class);
    }

    /**
     * @return array<CredentialRecord>
     */
    public function findAllForUserEntity(PublicKeyCredentialUserEntity $publicKeyCredentialUserEntity): array
    {
        return $this->findBy(['userHandle' => $publicKeyCredentialUserEntity->id]);
    }

    public function findOneByCredentialId(string $publicKeyCredentialId): ?CredentialRecord
    {
        // Die Credential-ID kommt roh herein, liegt in der Datenbank aber base64-kodiert
        // (DBAL-Typ `base64`). Ohne die Kodierung findet die Abfrage nie etwas.
        return $this->findOneBy(['publicKeyCredentialId' => base64_encode($publicKeyCredentialId)]);
    }

    /**
     * Wird sowohl nach der Registrierung eines neuen Passkeys als auch nach jeder
     * Anmeldung aufgerufen. Im ersten Fall kommt ein blankes CredentialRecord herein, das
     * erst einem Nutzer zugeordnet werden muss; im zweiten reicht es, den Signaturzähler
     * fortzuschreiben.
     */
    public function saveCredentialRecord(CredentialRecord $credentialRecord): void
    {
        $entityManager = $this->getEntityManager();

        if ($credentialRecord instanceof WebauthnCredential) {
            $entityManager->persist($credentialRecord);
            $entityManager->flush();

            return;
        }

        $existingCredential = $this->findOneByCredentialId($credentialRecord->publicKeyCredentialId);

        if ($existingCredential instanceof WebauthnCredential) {
            $existingCredential->markUsed($credentialRecord->counter);

            $entityManager->flush();

            return;
        }

        $user = $this->userRepository->findOneBy(['webauthnUserHandle' => $credentialRecord->userHandle]);

        if (!$user instanceof User) {
            // Ohne zugehörigen Nutzer wäre das Credential eine Waise, die sich später
            // nicht mehr zuordnen liesse. Lieber gar nicht erst speichern.
            return;
        }

        $credential = WebauthnCredential::createFromRecord($credentialRecord, $user, $this->generateName($user));

        $entityManager->persist($credential);
        $entityManager->flush();
    }

    /**
     * @return array<WebauthnCredential>
     */
    public function findAllForUser(User $user): array
    {
        return $this->findBy(['user' => $user], ['createdAt' => 'ASC']);
    }

    public function countForUser(User $user): int
    {
        return $this->count(['user' => $user]);
    }

    public function remove(WebauthnCredential $credential): void
    {
        $entityManager = $this->getEntityManager();

        $entityManager->remove($credential);
        $entityManager->flush();
    }

    /**
     * Vorbelegung, solange der Nutzer seinen Passkey noch nicht selbst benannt hat.
     */
    private function generateName(User $user): string
    {
        return sprintf('Passkey %d', $this->countForUser($user) + 1);
    }
}
