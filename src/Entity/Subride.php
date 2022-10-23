<?php declare(strict_types=1);

namespace App\Entity;

use App\Criticalmass\Router\Annotation as Routing;
use App\Criticalmass\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use App\EntityInterface\AuditableInterface;
use App\EntityInterface\RouteableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SubrideRepository")
 * @ORM\Table(name="subride")
 * @JMS\ExclusionPolicy("all")
 */
class Subride implements AuditableInterface, SocialNetworkProfileAble, RouteableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     * @Routing\RouteParameter(name="subrideId")
     */
    protected ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="Ride", inversedBy="subrides")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     * @JMS\Groups({"extended-subride-list"})
     * @Routing\RouteParameter(name="rideIdentifier")
     * @Routing\RouteParameter(name="citySlug")
     */
    protected ?Ride $ride = null;

    /**
     * @ORM\OneToMany(targetEntity="SocialNetworkProfile", mappedBy="subride", cascade={"persist", "remove"})
     */
    protected Collection $socialNetworkProfiles;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @JMS\Expose
     */
    protected ?string $title = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     */
    protected ?string $description = null;

    /**
     * @ORM\Column(type="datetime")
     * @JMS\Expose
     */
    protected ?\DateTime $dateTime = null;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @JMS\Expose
     */
    protected \DateTime $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Expose
     */
    protected ?\DateTime $updatedAt = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @JMS\Expose
     */
    protected ?string $location = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose
     */
    protected ?float $latitude = null;

    /**
     * @ORM\Column(type="float")
     * @JMS\Expose
     */
    protected ?float $longitude = null;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="subrides")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected ?User $user = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();

        $this->socialNetworkProfiles = new ArrayCollection();
    }

    public function __clone()
    {
        $this->setCreatedAt(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setTitle(string $title): Subride
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setDescription(string $description): Subride
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("timestamp")
     * @JMS\Type("integer")
     */
    public function getTimestamp(): int
    {
        return (int) $this->dateTime->format('U');
    }

    public function setDateTime(\DateTime $dateTime): Subride
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setLocation(string $location): Subride
    {
        $this->location = $location;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLatitude(float $latitude): Subride
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLongitude(float $longitude): Subride
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setRide(Ride $ride = null): Subride
    {
        $this->ride = $ride;

        return $this;
    }

    public function getRide(): Ride
    {
        return $this->ride;
    }

    public function setUser(User $user = null): Subride
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setCreatedAt(\DateTime $createdAt): Subride
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt = null): Subride
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function getTime(): \DateTime
    {
        return $this->dateTime;
    }

    /** @deprecated */
    public function setTime(\DateTime $time): Subride
    {
        $this->dateTime = new \DateTime($this->dateTime->format('Y-m-d') . ' ' . $time->format('H:i:s'));

        return $this;
    }

    public function addSocialNetworkProfile(SocialNetworkProfile $socialNetworkProfile): Subride
    {
        $this->socialNetworkProfiles->add($socialNetworkProfile);

        return $this;
    }

    public function setSocialNetworkProfiles(Collection $socialNetworkProfiles): Subride
    {
        $this->socialNetworkProfiles = $socialNetworkProfiles;

        return $this;
    }

    public function getSocialNetworkProfiles(): Collection
    {
        return $this->socialNetworkProfiles;
    }

    public function removeSocialNetworkProfile(SocialNetworkProfile $socialNetworkProfile): Subride
    {
        $this->socialNetworkProfiles->removeElement($socialNetworkProfile);

        return $this;
    }
}
