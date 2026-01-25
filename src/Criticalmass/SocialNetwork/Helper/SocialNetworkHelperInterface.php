<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\Helper;

use App\Entity\City;
use App\Entity\Ride;
use App\Entity\SocialNetworkProfile;
use App\Entity\Subride;
use App\Entity\User;
use App\EntityInterface\SocialNetworkProfileAble;

interface SocialNetworkHelperInterface
{
    public function getProfileAbleObject(?Ride $ride = null, ?Subride $subride = null, ?City $city = null, ?User $user = null): SocialNetworkProfileAble;
    public function getProfileAble(SocialNetworkProfile $socialNetworkProfile): ?SocialNetworkProfileAble;
    public function getProfileAbleShortname(SocialNetworkProfileAble $profileAble): string;
    public function getRouteName(SocialNetworkProfileAble $profileAble, string $actionName): string;
    public function getProfileList(SocialNetworkProfileAble $profileAble): array;
}
