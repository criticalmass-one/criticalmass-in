<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Webauthn\CredentialRecord;

/**
 * Ein registrierter Passkey.
 *
 * Die kryptografischen Felder (publicKeyCredentialId, credentialPublicKey, transports,
 * trustPath, aaguid, counter, …) erbt diese Klasse von der Mapped Superclass
 * `Webauthn\CredentialRecord`, deren XML-Zuordnung das Bundle mitliefert und die in
 * config/packages/doctrine.yaml als eigenes Mapping eingebunden ist.
 */
#[ORM\Table(name: 'webauthn_credential')]
// Der DBAL-Typ `base64` legt publicKeyCredentialId als LONGTEXT an; MariaDB verlangt für
// einen Index darauf eine Präfixlänge. 255 Zeichen entsprechen 191 Rohbytes und liegen
// weit über allem, was Authenticator in der Praxis als Credential-ID ausgeben.
#[ORM\UniqueConstraint(name: 'uniq_webauthn_credential_id', columns: ['publicKeyCredentialId'], options: ['lengths' => [255]])]
#[ORM\Index(name: 'idx_webauthn_user_handle', columns: ['userHandle'])]
#[ORM\Entity(repositoryClass: 'App\Repository\WebauthnCredentialRepository')]
#[ORM\HasLifecycleCallbacks]
class WebauthnCredential extends CredentialRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\User')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    protected ?User $user = null;

    /** Vom Nutzer vergebener Name, damit sich mehrere Passkeys unterscheiden lassen. */
    #[ORM\Column(type: 'string', length: 255)]
    protected string $name = 'Passkey';

    #[ORM\Column(type: 'datetime')]
    protected ?\DateTime $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTime $lastUsedAt = null;

    /**
     * Der Authenticator liefert beim Registrieren ein blankes CredentialRecord. Erst hier
     * bekommt es einen Nutzer und einen Namen und wird damit persistierbar.
     */
    public static function createFromRecord(CredentialRecord $record, User $user, string $name): self
    {
        $credential = new self(
            $record->publicKeyCredentialId,
            $record->type,
            $record->transports,
            $record->attestationType,
            $record->trustPath,
            $record->aaguid,
            $record->credentialPublicKey,
            $record->userHandle,
            $record->counter,
            $record->otherUI,
            $record->backupEligible,
            $record->backupStatus,
            $record->uvInitialized,
        );

        $credential->user = $user;
        $credential->name = $name;

        return $credential;
    }

    #[ORM\PrePersist]
    public function prePersist(): self
    {
        $this->createdAt = new \DateTime();

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function getLastUsedAt(): ?\DateTime
    {
        return $this->lastUsedAt;
    }

    public function setLastUsedAt(?\DateTime $lastUsedAt): self
    {
        $this->lastUsedAt = $lastUsedAt;

        return $this;
    }

    /**
     * Nach einer erfolgreichen Anmeldung: Signaturzähler des Authenticators übernehmen und
     * die Nutzung protokollieren.
     */
    public function markUsed(int $counter): self
    {
        $this->counter = $counter;
        $this->lastUsedAt = new \DateTime();

        return $this;
    }
}
