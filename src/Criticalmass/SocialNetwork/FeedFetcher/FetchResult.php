<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher;

use App\Entity\SocialNetworkProfile;

class FetchResult
{
    protected SocialNetworkProfile $socialNetworkProfile;

    protected string $status;

    protected int $counter = 0;

    public function getSocialNetworkProfile(): SocialNetworkProfile
    {
        return $this->socialNetworkProfile;
    }

    public function setSocialNetworkProfile(SocialNetworkProfile $socialNetworkProfile): FetchResult
    {
        $this->socialNetworkProfile = $socialNetworkProfile;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): FetchResult
    {
        $this->status = $status;

        return $this;
    }

    public function getCounter(): int
    {
        return $this->counter;
    }

    public function setCounter(int $counter): FetchResult
    {
        $this->counter = $counter;

        return $this;
    }
}