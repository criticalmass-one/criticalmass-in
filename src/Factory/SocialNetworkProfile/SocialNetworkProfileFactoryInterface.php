<?php declare(strict_types=1);

namespace App\Factory\SocialNetworkProfile;

use App\Entity\City;
use App\Entity\Ride;
use App\Entity\SocialNetworkProfile;
use App\Entity\Subride;
use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

interface SocialNetworkProfileFactoryInterface
{
    public function withEnabled(bool $enabled): SocialNetworkProfileFactoryInterface;
    public function withMainNetwork(bool $mainNetwork): SocialNetworkProfileFactoryInterface;
    public function withRide(Ride $ride): SocialNetworkProfileFactoryInterface;
    public function withSubride(Subride $subride): SocialNetworkProfileFactoryInterface;
    public function withCity(City $city): SocialNetworkProfileFactoryInterface;
    public function withUser(UserInterface $user): SocialNetworkProfileFactoryInterface;
    public function withCreatedAt(\DateTime $createdAt): SocialNetworkProfileFactoryInterface;
    public function withCreatedBy(User $user): SocialNetworkProfileFactoryInterface;
    public function build(): SocialNetworkProfile;
}