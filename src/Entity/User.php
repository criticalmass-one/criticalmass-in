<?php declare(strict_types=1);

namespace App\Entity;

use App\Criticalmass\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use App\EntityInterface\PhotoInterface;
use App\EntityInterface\RouteableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\LegacyPasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Vich\Uploadable
 */
#[ORM\Table(name: 'fos_user_user')]
#[ORM\Entity(repositoryClass: 'App\Repository\UserRepository')]
#[ORM\HasLifecycleCallbacks]
#[JMS\ExclusionPolicy('all')]
class User implements SocialNetworkProfileAble, RouteableInterface, PhotoInterface, UserInterface, LegacyPasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[JMS\Groups(['timelapse'])]
    #[JMS\Expose]
    protected ?int $id = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $roles = [];

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Regex(pattern: '/https?\:\/\//', match: false, message: 'Der Benutzername darf keine Url enthalten')]
    #[JMS\Groups(['timelapse'])]
    #[JMS\Expose]
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $username = null;

    #[ORM\OneToMany(targetEntity: 'Track', mappedBy: 'user', cascade: ['persist', 'remove'])]
    protected Collection $tracks;

    #[ORM\Column(type: 'smallint', nullable: true)]
    #[JMS\Groups(['timelapse'])]
    #[JMS\Expose]
    protected int $colorRed = 0;

    #[ORM\Column(type: 'smallint', nullable: true)]
    #[JMS\Groups(['timelapse'])]
    #[JMS\Expose]
    protected int $colorGreen = 0;

    #[ORM\Column(type: 'smallint', nullable: true)]
    #[JMS\Groups(['timelapse'])]
    #[JMS\Expose]
    protected int $colorBlue = 0;

    #[ORM\Column(type: 'boolean', nullable: true, options: ['default' => 0])]
    protected bool $blurGalleries = false;

    #[ORM\Column(type: 'boolean', nullable: true, options: ['default' => 0])]
    protected bool $enabled = false;

    #[ORM\OneToMany(targetEntity: 'Participation', mappedBy: 'user')]
    protected Collection $participations;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTime $updatedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    protected ?\DateTime $createdAt = null;

    #[ORM\Column(name: 'facebook_id', type: 'string', length: 255, nullable: true)]
    protected ?string $facebookId = null;

    #[ORM\Column(name: 'facebook_access_token', type: 'text', nullable: true)]
    protected ?string $facebookAccessToken = null;

    #[ORM\Column(name: 'strava_id', type: 'string', length: 255, nullable: true)]
    protected ?string $stravaId = null;

    #[ORM\Column(name: 'strava_access_token', type: 'text', nullable: true)]
    protected ?string $stravaAccessToken = null;

    #[ORM\Column(name: 'twitter_id', type: 'string', length: 255, nullable: true)]
    protected ?string $twitterId = null;

    #[ORM\Column(name: 'twitter_access_token', type: 'text', nullable: true)]
    protected ?string $twitterkAccessToken = null;

    #[ORM\OneToMany(targetEntity: 'CityCycle', mappedBy: 'city', cascade: ['persist', 'remove'])]
    protected Collection $cycles;

    /**
     * @Vich\UploadableField(mapping="user_photo", fileNameProperty="imageName", size="imageSize", mimeType="imageMimeType")
     */
    protected ?File $imageFile = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[JMS\Groups(['timelapse'])]
    #[JMS\Expose]
    protected ?string $imageName = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    protected ?int $imageSize = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $imageMimeType = null;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    protected bool $ownProfilePhoto = false;

    #[ORM\OneToMany(targetEntity: 'App\Entity\SocialNetworkProfile', mappedBy: 'createdBy')]
    private Collection $socialNetworkProfiles;

    #[ORM\OneToMany(targetEntity: 'App\Entity\TrackImportCandidate', mappedBy: 'user', orphanRemoval: true)]
    private Collection $trackImportCandidates;

    public function __construct()
    {
        $this->colorRed = rand(0, 255);
        $this->colorGreen = rand(0, 255);
        $this->colorBlue = rand(0, 255);
        $this->createdAt = new \DateTime();

        $this->tracks = new ArrayCollection();
        $this->participations = new ArrayCollection();
        $this->socialNetworkProfiles = new ArrayCollection();
        $this->trackImportCandidates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): User
    {
        $this->id = $id;

        return $this;
    }


    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles = []): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getColorRed(): int
    {
        return $this->colorRed;
    }

    public function setColorRed(int $colorRed): User
    {
        $this->colorRed = $colorRed;

        return $this;
    }

    public function getColorGreen(): int
    {
        return $this->colorGreen;
    }

    public function setColorGreen(int $colorGreen): User
    {
        $this->colorGreen = $colorGreen;

        return $this;
    }

    public function getColorBlue(): int
    {
        return $this->colorBlue;
    }

    public function setColorBlue(int $colorBlue): User
    {
        $this->colorBlue = $colorBlue;

        return $this;
    }

    /**
     * @deprecated
     */
    public function equals(User $user): bool
    {
        return $user->getId() == $this->getId();
    }

    public function addTrack(Track $track): User
    {
        $this->tracks->add($track);

        return $this;
    }

    public function removeTrack(Track $tracks): User
    {
        $this->tracks->removeElement($tracks);

        return $this;
    }

    public function getTracks(): Collection
    {
        return $this->tracks;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): User
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): User
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function prePersist(): User
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();

        return $this;
    }

    #[ORM\PreUpdate]
    public function preUpdate(): User
    {
        $this->updatedAt = new \DateTime();

        return $this;
    }

    public function addParticipation(Participation $participation): User
    {
        $this->participations->add($participation);

        return $this;
    }

    public function removeParticipation(Participation $participation): User
    {
        $this->participations->removeElement($participation);

        return $this;
    }

    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function setStravaId(string $stravaId): User
    {
        $this->stravaId = $stravaId;

        return $this;
    }

    public function getStravaId(): ?string
    {
        return $this->stravaId;
    }

    public function setStravaAccessToken(string $stravaAccessToken): User
    {
        $this->stravaAccessToken = $stravaAccessToken;

        return $this;
    }

    public function getStravaAccessToken(): ?string
    {
        return $this->stravaAccessToken;
    }

    public function setFacebookId(string $facebookId): User
    {
        $this->facebookId = $facebookId;

        return $this;
    }

    public function getFacebookId(): ?string
    {
        return $this->facebookId;
    }

    public function setFacebookAccessToken(string $facebookAccessToken): User
    {
        $this->facebookAccessToken = $facebookAccessToken;

        return $this;
    }

    public function getFacebookAccessToken(): ?string
    {
        return $this->facebookAccessToken;
    }

    public function setTwitterId(string $twitterId): User
    {
        $this->twitterId = $twitterId;

        return $this;
    }

    public function getTwitterId(): ?string
    {
        return $this->twitterId;
    }

    public function setTwitterAccessToken(string $twitterkAccessToken): User
    {
        $this->twitterkAccessToken = $twitterkAccessToken;

        return $this;
    }

    public function getTwitterAccessToken(): ?string
    {
        return $this->twitterkAccessToken;
    }

    public function setBlurGalleries(bool $blurGalleries): User
    {
        $this->blurGalleries = $blurGalleries;

        return $this;
    }

    public function getBlurGalleries(): bool
    {
        return $this->blurGalleries;
    }

    public function isOauthAccount(): bool
    {
        return $this->stravaId || $this->facebookId || $this->isTwitterAccount();
    }

    public function isFacebookAccount(): bool
    {
        return $this->facebookId !== null;
    }

    public function isStravaAccount(): bool
    {
        return $this->stravaId !== null;
    }

    public function isTwitterAccount(): bool
    {
        return $this->twitterId !== null;
    }

    public function addCycle(CityCycle $cityCycle): User
    {
        $this->cycles->add($cityCycle);

        return $this;
    }

    public function setCycles(Collection $cityCycles): User
    {
        $this->cycles = $cityCycles;

        return $this;
    }

    public function getCycles(): Collection
    {
        return $this->cycles;
    }

    public function removeCycle(CityCycle $cityCycle): User
    {
        $this->cycles->removeElement($cityCycle);

        return $this;
    }

    public function setImageFile(File $image = null): PhotoInterface
    {
        $this->imageFile = $image;

        if ($image) {
            $this->updatedAt = new \DateTime('now');
        }

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(string $imageName = null): PhotoInterface
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    public function setImageSize(int $imageSize = null): PhotoInterface
    {
        $this->imageSize = $imageSize;

        return $this;
    }

    public function getImageMimeType(): ?string
    {
        return $this->imageMimeType;
    }

    public function setImageMimeType(string $imageMimeType = null): PhotoInterface
    {
        $this->imageMimeType = $imageMimeType;

        return $this;
    }

    public function hasOwnProfilePhoto(): bool
    {
        return $this->ownProfilePhoto;
    }

    public function setOwnProfilePhoto(bool $ownProfilePhoto): User
    {
        $this->ownProfilePhoto = $ownProfilePhoto;

        return $this;
    }

    /**
     * @return Collection|SocialNetworkProfile[]
     */
    public function getSocialNetworkProfiles(): Collection
    {
        return $this->socialNetworkProfiles;
    }

    public function addSocialNetworkProfile(SocialNetworkProfile $socialNetworkProfile): self
    {
        if (!$this->socialNetworkProfiles->contains($socialNetworkProfile)) {
            $this->socialNetworkProfiles[] = $socialNetworkProfile;
            $socialNetworkProfile->setCreatedBy($this);
        }

        return $this;
    }

    public function removeSocialNetworkProfile(SocialNetworkProfile $socialNetworkProfile): self
    {
        if ($this->socialNetworkProfiles->contains($socialNetworkProfile)) {
            $this->socialNetworkProfiles->removeElement($socialNetworkProfile);
            // set the owning side to null (unless already changed)
            if ($socialNetworkProfile->getCreatedBy() === $this) {
                $socialNetworkProfile->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TrackImportCandidate[]
     */
    public function getTrackImportCandidates(): Collection
    {
        return $this->trackImportCandidates;
    }

    public function addTrackImportCandidate(TrackImportCandidate $trackImportCandidate): self
    {
        if (!$this->trackImportCandidates->contains($trackImportCandidate)) {
            $this->trackImportCandidates[] = $trackImportCandidate;
            $trackImportCandidate->setUser($this);
        }

        return $this;
    }

    public function removeTrackImportCandidate(TrackImportCandidate $trackImportCandidate): self
    {
        if ($this->trackImportCandidates->contains($trackImportCandidate)) {
            $this->trackImportCandidates->removeElement($trackImportCandidate);
            // set the owning side to null (unless already changed)
            if ($trackImportCandidate->getUser() === $this) {
                $trackImportCandidate->setUser(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): User
    {
        $this->email = $email;

        return $this;
    }

    public function getTwitterkAccessToken(): ?string
    {
        return $this->twitterkAccessToken;
    }

    public function setTwitterkAccessToken(?string $twitterkAccessToken): User
    {
        $this->twitterkAccessToken = $twitterkAccessToken;

        return $this;
    }

    public function getSalt(): string
    {
        return '';
    }

    public function setSalt(string $salt): self
    {
        return $this;
    }

    public function getPassword(): string
    {
        return '';
    }

    public function setPassword(string $password): self
    {
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }
}
