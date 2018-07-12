<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="social_network_profile")
 * @ORM\Entity(repositoryClass="App\Repository\SocialNetworkProfileRepository")
 */
class SocialNetworkProfile
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="socialNetworkProfiles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="City", inversedBy="socialNetworkProfiles")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    /**
     * @ORM\ManyToOne(targetEntity="Ride", inversedBy="socialNetworkProfiles")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
     */
    protected $ride;

    /**
     * @ORM\ManyToOne(targetEntity="Subride", inversedBy="socialNetworkProfiles")
     * @ORM\JoinColumn(name="subride_id", referencedColumnName="id")
     */
    protected $subride;

    /**
     * @ORM\Column(type="string")
     */
    protected $identifier;

    /**
     * @ORM\Column(type="string")
     */
    protected $network;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $mainNetwork = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled = true;

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

    public function getMainNetwork(): bool
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
}
