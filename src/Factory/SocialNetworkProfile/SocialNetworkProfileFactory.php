<?php declare(strict_types=1);

namespace App\Factory\SocialNetworkProfile;

use App\Entity\City;
use App\Entity\Ride;
use App\Entity\SocialNetworkProfile;
use App\Entity\Subride;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class SocialNetworkProfileFactory implements SocialNetworkProfileFactoryInterface
{
    /**
     * @var SocialNetworkProfile $socialNetworkProfile
     */
    protected $socialNetworkProfile;

    public function __construct()
    {
        $this->socialNetworkProfile = new SocialNetworkProfile();

        $this->socialNetworkProfile
            ->setEnabled(true)
            ->setMainNetwork(false)
            ->setCreatedAt(new \DateTime());
    }

    public function withEnabled(bool $enabled): SocialNetworkProfileFactoryInterface
    {
        $this->socialNetworkProfile->setEnabled($enabled);

        return $this;
    }

    public function withMainNetwork(bool $mainNetwork): SocialNetworkProfileFactoryInterface
    {
        $this->socialNetworkProfile->setMainNetwork($mainNetwork);

        return $this;
    }

    public function withRide(Ride $ride): SocialNetworkProfileFactoryInterface
    {
        $this->socialNetworkProfile->setRide($ride);

        return $this;
    }

    public function withSubride(Subride $subride): SocialNetworkProfileFactoryInterface
    {
        $this->socialNetworkProfile->setSubride($subride);

        return $this;
    }

    public function withCity(City $city): SocialNetworkProfileFactoryInterface
    {
        $this->socialNetworkProfile->setCity($city);

        return $this;
    }

    public function withUser(UserInterface $user): SocialNetworkProfileFactoryInterface
    {
        $this->socialNetworkProfile->setUser($user);

        return $this;
    }

    public function withCreatedAt(\DateTime $createdAt): SocialNetworkProfileFactoryInterface
    {
        $this->socialNetworkProfile->setCreatedAt($createdAt);

        return $this;
    }

    public function withCreatedBy(User $user): SocialNetworkProfileFactoryInterface
    {
        $this->socialNetworkProfile->setCreatedBy($user);

        return $this;
    }

    public function build(): SocialNetworkProfile
    {
        return $this->socialNetworkProfile;
    }
}