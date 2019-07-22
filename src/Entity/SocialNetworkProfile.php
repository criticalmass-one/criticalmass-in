<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="social_network_profile")
 * @ORM\Entity(repositoryClass="App\Repository\SocialNetworkProfileRepository")
 */
class SocialNetworkProfile
{
    /**
     * @var int $id
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var User $user
     * @ORM\ManyToOne(targetEntity="User", inversedBy="socialNetworkProfiles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @var City $city
     * @ORM\ManyToOne(targetEntity="City", inversedBy="socialNetworkProfiles")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    /**
     * @var Ride $ride
     * @ORM\ManyToOne(targetEntity="Ride", inversedBy="socialNetworkProfiles")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected $ride;

    /**
     * @var Subride $subride
     * @ORM\ManyToOne(targetEntity="Subride", inversedBy="socialNetworkProfiles")
     * @ORM\JoinColumn(name="subride_id", referencedColumnName="id")
     */
    protected $subride;

    /**
     * @var string $identifier
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    protected $identifier;

    /**
     * @var string $network
     * @ORM\Column(type="string")
     */
    protected $network;

    /**
     * @var bool $mainNetwork
     * @ORM\Column(type="boolean")
     */
    protected $mainNetwork;

    /**
     * @var bool $enabled
     * @ORM\Column(type="boolean")
     */
    protected $enabled;

    /**
     * @var \DateTime $createdAt
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var User $createdBy
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="socialNetworkProfiles")
     */
    private $createdBy;

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
}
