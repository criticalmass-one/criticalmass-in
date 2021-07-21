<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="social_network_profile", options={"charset"="utf8mb4", "collate"="utf8mb4_unicode_ci"})
 * @ORM\Entity(repositoryClass="App\Repository\SocialNetworkProfileRepository")
 * @JMS\ExclusionPolicy("all")
 */
class SocialNetworkProfile
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="socialNetworkProfiles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected ?User $user = null;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="socialNetworkProfiles")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     * @JMS\Expose
     * @JMS\Type("Relation<App\Entity\City>")
     * @JMS\SerializedName("city_id")
     */
    protected ?City $city = null;

    /**
     * @ORM\ManyToOne(targetEntity="Ride", inversedBy="socialNetworkProfiles")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected ?Ride $ride = null;

    /**
     * @ORM\ManyToOne(targetEntity="Subride", inversedBy="socialNetworkProfiles")
     * @ORM\JoinColumn(name="subride_id", referencedColumnName="id")
     */
    protected ?Subride $subride = null;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected ?string $identifier = null;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected ?string $network = null;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $mainNetwork = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $enabled = true;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    private ?\DateTime $createdAt = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="socialNetworkProfiles")
     */
    private ?User $createdBy = null;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected bool $autoPublish = true;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected ?\DateTime $lastFetchSuccessDateTime = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected ?\DateTime $lastFetchFailureDateTime = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected ?string $lastFetchFailureError = null;

    /**
     * @ORM\Column(type="boolean")
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $autoFetch = true;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     * @JMS\Groups({"ride-list"})
     */
    protected $additionalData;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): SocialNetworkProfile
    {
        $this->id = $id;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user = null): SocialNetworkProfile
    {
        $this->user = $user;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(City $city = null): SocialNetworkProfile
    {
        $this->city = $city;

        return $this;
    }

    public function getRide(): ?Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride = null): SocialNetworkProfile
    {
        $this->ride = $ride;

        return $this;
    }

    public function getSubride(): ?Subride
    {
        return $this->subride;
    }

    public function setSubride(Subride $subride = null): SocialNetworkProfile
    {
        $this->subride = $subride;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): SocialNetworkProfile
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getNetwork(): ?string
    {
        return $this->network;
    }

    public function setNetwork($network): SocialNetworkProfile
    {
        $this->network = $network;

        return $this;
    }

    /**
     * @deprecated
     */
    public function getMainNetwork(): bool
    {
        return $this->mainNetwork;
    }

    public function isMainNetwork(): bool
    {
        return $this->mainNetwork;
    }

    public function setMainNetwork(bool $mainNetwork): SocialNetworkProfile
    {
        $this->mainNetwork = $mainNetwork;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): SocialNetworkProfile
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function isAutoPublish(): bool
    {
        return $this->autoPublish;
    }

    public function setAutoPublish(bool $autoPublish): SocialNetworkProfile
    {
        $this->autoPublish = $autoPublish;

        return $this;
    }

    public function getLastFetchSuccessDateTime(): ?\DateTimeInterface
    {
        return $this->lastFetchSuccessDateTime;
    }

    public function setLastFetchSuccessDateTime(?\DateTimeInterface $lastFetchSuccessDateTime): self
    {
        $this->lastFetchSuccessDateTime = $lastFetchSuccessDateTime;

        return $this;
    }

    public function getLastFetchFailureDateTime(): ?\DateTimeInterface
    {
        return $this->lastFetchFailureDateTime;
    }

    public function setLastFetchFailureDateTime(?\DateTimeInterface $lastFetchFailureDateTime): self
    {
        $this->lastFetchFailureDateTime = $lastFetchFailureDateTime;

        return $this;
    }

    public function getLastFetchFailureError(): ?string
    {
        return $this->lastFetchFailureError;
    }

    public function setLastFetchFailureError(?string $lastFetchFailureError): self
    {
        $this->lastFetchFailureError = $lastFetchFailureError;

        return $this;
    }

    public function getAutoFetch(): ?bool
    {
        return $this->autoFetch;
    }

    public function setAutoFetch(bool $autoFetch): self
    {
        $this->autoFetch = $autoFetch;

        return $this;
    }

    public function getAdditionalData(): ?array
    {
        return (array)json_decode($this->additionalData ?? '{}');
    }

    public function setAdditionalData(?array $additionalData): self
    {
        $this->additionalData = json_encode($additionalData);

        return $this;
    }
}
