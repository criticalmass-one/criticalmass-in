<?php declare(strict_types=1);

namespace App\Entity;

use App\Criticalmass\Router\Annotation as Routing;
use App\Criticalmass\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use App\EntityInterface\PhotoInterface;
use App\EntityInterface\RouteableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="fos_user_user")
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 * @JMS\ExclusionPolicy("all")
 */
class User extends BaseUser implements SocialNetworkProfileAble, RouteableInterface, PhotoInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     * @todo Add typed property
     * @var int $id
     */
    protected $id;

    /**
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     * @Assert\NotBlank()
     * @Assert\Regex(
     *     pattern="/https?\:\/\//",
     *     match=false,
     *     message="Der Benutzername darf keine Url enthalten"
     * )
     * @Routing\RouteParameter(name="username")
     * @todo Add typed property
     */
    protected $username;

    /**
     * @ORM\OneToMany(targetEntity="Track", mappedBy="user", cascade={"persist", "remove"})
     */
    protected Collection $tracks;

    /**
     * @ORM\Column(type="smallint")
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected int $colorRed = 0;

    /**
     * @ORM\Column(type="smallint")
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected int $colorGreen = 0;

    /**
     * @ORM\Column(type="smallint")
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     */
    protected int $colorBlue = 0;

    /**
     * @ORM\Column(type="boolean", options={"default" = 0})
     */
    protected bool $blurGalleries = false;

    /**
     * @ORM\OneToMany(targetEntity="Participation", mappedBy="user")
     */
    protected Collection $participations;

    /**
     * @ORM\Column(type="datetime")
     */
    protected ?\DateTime $updatedAt = null;

    /**
     * @ORM\Column(type="datetime")
     */
    protected ?\DateTime $createdAt = null;

    /**
     * @ORM\Column(name="facebook_id", type="string", length=255, nullable=true)
     */
    protected ?string $facebookId = null;

    /**
     * @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true)
     */
    protected ?string $facebookAccessToken = null;

    /**
     * @ORM\Column(name="strava_id", type="string", length=255, nullable=true)
     */
    protected ?string $stravaId = null;

    /**
     * @ORM\Column(name="strava_access_token", type="string", length=255, nullable=true)
     */
    protected ?string $stravaAccessToken = null;

    /**
     * @ORM\Column(name="runkeeper_id", type="string", length=255, nullable=true)
     */
    protected ?string $runkeeperId = null;

    /**
     * @ORM\Column(name="runkeeper_access_token", type="string", length=255, nullable=true)
     */
    protected ?string $runkeeperAccessToken = null;

    /**
     * @ORM\Column(name="twitter_id", type="string", length=255, nullable=true)
     */
    protected ?string $twitterId = null;

    /**
     * @ORM\Column(name="twitter_access_token", type="string", length=255, nullable=true)
     */
    protected ?string $twitterkAccessToken = null;

    /**
     * @ORM\OneToMany(targetEntity="CityCycle", mappedBy="city", cascade={"persist", "remove"})
     */
    protected Collection $cycles;

    /**
     * @Vich\UploadableField(mapping="user_photo", fileNameProperty="imageName", size="imageSize", mimeType="imageMimeType")
     */
    protected ?File $imageFile = null;

    /**
     * @JMS\Groups({"timelapse"})
     * @JMS\Expose
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected ?string $imageName = null;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected ?int $imageSize = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected ?string $imageMimeType = null;

    /**
     * @ORM\Column(type="boolean", options={"default" = 0})
     */
    protected bool $ownProfilePhoto = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SocialNetworkProfile", mappedBy="createdBy")
     */
    private Collection $socialNetworkProfiles;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Heatmap", mappedBy="user", cascade={"persist", "remove"})
     */
    private ?Heatmap $heatmap = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\TrackImportCandidate", mappedBy="user", orphanRemoval=true)
     */
    private Collection $trackImportCandidates;

    public function __construct()
    {
        parent::__construct();

        $this->colorRed = rand(0, 255);
        $this->colorGreen = rand(0, 255);
        $this->colorBlue = rand(0, 255);

        $this->tracks = new ArrayCollection();
        $this->participations = new ArrayCollection();
        $this->socialNetworkProfiles = new ArrayCollection();
        $this->trackImportCandidates = new ArrayCollection();
    }

    public function setId(int $id): User
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @ORM\PrePersist()
     */
    public function prePersist(): User
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();

        return $this;
    }

    /**
     * @ORM\PreUpdate()
     */
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

    public function setRunkeeperId(string $runkeeperId): User
    {
        $this->runkeeperId = $runkeeperId;

        return $this;
    }

    public function getRunkeeperId(): ?string
    {
        return $this->runkeeperId;
    }

    public function setRunkeeperAccessToken(string $runkeeperAccessToken): User
    {
        $this->runkeeperAccessToken = $runkeeperAccessToken;

        return $this;
    }

    public function getRunkeeperAccessToken(): ?string
    {
        return $this->runkeeperAccessToken;
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
        return $this->runkeeperId || $this->stravaId || $this->facebookId || $this->isTwitterAccount();
    }

    public function isFacebookAccount(): bool
    {
        return $this->facebookId !== null;
    }

    public function isStravaAccount(): bool
    {
        return $this->stravaId !== null;
    }

    public function isRunkeeperAccount(): bool
    {
        return $this->facebookId !== null;
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

    public function getHeatmap(): ?Heatmap
    {
        return $this->heatmap;
    }

    public function setHeatmap(?Heatmap $heatmap): self
    {
        $this->heatmap = $heatmap;

        // set (or unset) the owning side of the relation if necessary
        $newUser = $heatmap === null ? null : $this;
        if ($newUser !== $heatmap->getUser()) {
            $heatmap->setUser($newUser);
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
}
