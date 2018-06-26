<?php

namespace AppBundle\Criticalmass\Timeline\Item;

use AppBundle\Entity\City;
use AppBundle\Entity\Ride;
use AppBundle\Entity\SocialNetworkProfile;
use AppBundle\Entity\Subride;
use AppBundle\Entity\User;

class SocialNetworkFeedItemItem extends AbstractItem
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

    /** @var string $permalink */
    protected $permalink;

    /** @var string $title */
    protected $title;

    /** @var string $text */
    protected $text;

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(City $city = null): SocialNetworkFeedItemItem
    {
        $this->city = $city;

        return $this;
    }

    public function getRide(): ?Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride = null): SocialNetworkFeedItemItem
    {
        $this->ride = $ride;

        return $this;
    }

    public function getSubride(): ?Subride
    {
        return $this->subride;
    }

    public function setSubride(Subride $subride = null): SocialNetworkFeedItemItem
    {
        $this->subride = $subride;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user = null): SocialNetworkFeedItemItem
    {
        $this->user = $user;

        return $this;
    }

    public function getSocialNetworkProfile(): SocialNetworkProfile
    {
        return $this->socialNetworkProfile;
    }

    public function setSocialNetworkProfile(SocialNetworkProfile $socialNetworkProfile): SocialNetworkFeedItemItem
    {
        $this->socialNetworkProfile = $socialNetworkProfile;

        return $this;
    }

    public function getPermalink(): string
    {
        return $this->permalink;
    }

    public function setPermalink(string $permalink): SocialNetworkFeedItemItem
    {
        $this->permalink = $permalink;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): SocialNetworkFeedItemItem
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): SocialNetworkFeedItemItem
    {
        $this->text = $text;

        return $this;
    }
}
