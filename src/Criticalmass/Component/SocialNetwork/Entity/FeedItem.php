<?php

namespace Criticalmass\Component\SocialNetwork\Entity;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Criticalmass\Bundle\AppBundle\Entity\SocialNetworkProfile;
use Criticalmass\Bundle\AppBundle\Entity\Subride;
use Criticalmass\Bundle\AppBundle\Entity\User;

class FeedItem
{
    /** @var User $user */
    protected $user;

    /** @var City $city */
    protected $city;

    /** @var Ride $ride */
    protected $ride;

    /** @var Subride $subride */
    protected $subride;

    /** @var SocialNetworkProfile $socialNetworkProfile */
    protected $socialNetworkProfile;

    /** @var string $uniqueIdentifier */
    protected $uniqueIdentifier;

    /** @var string $title */
    protected $title;

    /** @var string $text */
    protected $text;

    /** @var \DateTime $dateTime */
    protected $dateTime;

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): FeedItem
    {
        $this->user = $user;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(City $city): FeedItem
    {
        $this->city = $city;

        return $this;
    }

    public function getRide(): ?Ride
    {
        return $this->ride;
    }

    public function setRide(Ride $ride): FeedItem
    {
        $this->ride = $ride;

        return $this;
    }

    public function getSubride(): ?Subride
    {
        return $this->subride;
    }

    public function setSubride(Subride $subride): FeedItem
    {
        $this->subride = $subride;

        return $this;
    }

    public function getSocialNetworkProfile(): SocialNetworkProfile
    {
        return $this->socialNetworkProfile;
    }

    public function setSocialNetworkProfile(SocialNetworkProfile $socialNetworkProfile): FeedItem
    {
        $this->socialNetworkProfile = $socialNetworkProfile;

        return $this;
    }

    public function getUniqueIdentifier(): string
    {
        return $this->uniqueIdentifier;
    }

    public function setUniqueIdentifier(string $uniqueIdentifier): FeedItem
    {
        $this->uniqueIdentifier = $uniqueIdentifier;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): FeedItem
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): FeedItem
    {
        $this->text = $text;

        return $this;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }

    public function setDateTime(\DateTime $dateTime): FeedItem
    {
        $this->dateTime = $dateTime;

        return $this;
    }


}
