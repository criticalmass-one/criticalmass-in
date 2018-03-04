<?php

namespace Criticalmass\Component\Timeline\Item;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\SocialNetworkProfile;
use Criticalmass\Bundle\AppBundle\Entity\Subride;
use Criticalmass\Bundle\AppBundle\Entity\User;

class FeedItemItem extends AbstractItem
{
    /** @var City $city */
    protected $city;

    /** @var Ride $ride */
    protected $ride;

    /** @var Subride $subride */
    protected $subride;

    /** @var User $user */
    protected $user;

    /** @var SocialNetworkProfile */
    protected $socialNetworkProfile;

    /** @var string $title */
    protected $title;

    /** @var string $text */
    protected $text;

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(City $city = null): FeedItemItem
    {
        $this->city = $city;

        return $this;
    }

    public function getRide(): ?Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride = null): FeedItemItem
    {
        $this->ride = $ride;

        return $this;
    }

    public function getSubride(): ?Subride
    {
        return $this->subride;
    }

    public function setSubride(Subride $subride = null): FeedItemItem
    {
        $this->subride = $subride;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user = null): FeedItemItem
    {
        $this->user = $user;

        return $this;
    }

    public function getSocialNetworkProfile(): SocialNetworkProfile
    {
        return $this->socialNetworkProfile;
    }

    public function setSocialNetworkProfile(SocialNetworkProfile $socialNetworkProfile): FeedItemItem
    {
        $this->socialNetworkProfile = $socialNetworkProfile;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): FeedItemItem
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): FeedItemItem
    {
        $this->text = $text;

        return $this;
    }
}
