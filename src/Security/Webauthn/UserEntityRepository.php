<?php declare(strict_types=1);

namespace App\Security\Webauthn;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;
use Webauthn\Bundle\Repository\CanRegisterUserEntity;
use Webauthn\Bundle\Repository\PublicKeyCredentialUserEntityRepositoryInterface;
use Webauthn\PublicKeyCredentialUserEntity;

/**
 * Übersetzt zwischen der App\Entity\User und dem, was WebAuthn einen Nutzer nennt.
 *
 * Zwei Kennungen sind dabei im Spiel und dürfen nicht verwechselt werden: der `name`
 * ist die E-Mail-Adresse, weil der Security-Provider darüber auflöst und der Passwort-
 * manager sie dem Nutzer anzeigt. Die `id` ist der User-Handle — eine eigene UUID, die
 * eine Adressänderung überlebt.
 */
readonly class UserEntityRepository implements PublicKeyCredentialUserEntityRepositoryInterface, CanRegisterUserEntity
{
    public function __construct(
        private UserRepository $userRepository,
        private ManagerRegistry $managerRegistry,
    ) {
    }

    public function findOneByUsername(string $username): ?PublicKeyCredentialUserEntity
    {
        $user = $this->userRepository->findOneBy(['email' => $username]);

        return $user instanceof User ? $this->createUserEntity($user) : null;
    }

    public function findOneByUserHandle(string $userHandle): ?PublicKeyCredentialUserEntity
    {
        $user = $this->userRepository->findOneBy(['webauthnUserHandle' => $userHandle]);

        return $user instanceof User ? $this->createUserEntity($user) : null;
    }

    /**
     * Passkeys lassen sich nur im eingeloggten Zustand anlegen, der Nutzer existiert an
     * dieser Stelle also längst. Es gibt nichts zu speichern.
     */
    public function saveUserEntity(PublicKeyCredentialUserEntity $userEntity): void
    {
    }

    public function createUserEntity(User $user): PublicKeyCredentialUserEntity
    {
        return PublicKeyCredentialUserEntity::create(
            (string) $user->getEmail(),
            $this->resolveUserHandle($user),
            (string) $user->getUsername(),
        );
    }

    /**
     * Bestandskonten haben noch keinen Handle. Er wird beim ersten Kontakt mit WebAuthn
     * erzeugt, damit die Migration ohne Backfill über alle Nutzer auskommt.
     */
    public function resolveUserHandle(User $user): string
    {
        $userHandle = $user->getWebauthnUserHandle();

        if (null !== $userHandle) {
            return $userHandle;
        }

        $userHandle = Uuid::v4()->toRfc4122();

        $user->setWebauthnUserHandle($userHandle);

        $this->managerRegistry->getManager()->flush();

        return $userHandle;
    }
}
