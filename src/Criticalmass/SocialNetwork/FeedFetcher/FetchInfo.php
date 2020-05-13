<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher;

class FetchInfo
{
    protected array $networkList = [];

    protected array $profileList = [];

    protected ?\DateTime $fromDateTime = null;

    protected ?\DateTime $untilDateTime = null;

    protected bool $includeFailedProfiles = false;

    protected bool $includeSuceededProfiles = true;

    protected bool $includeOldItems = false;

    public function hasNetworkList(): bool
    {
        return 0 !== count($this->networkList);
    }

    public function getNetworkList(): array
    {
        return $this->networkList;
    }

    public function setNetworkList(array $networkList): FetchInfo
    {
        $this->networkList = $networkList;

        return $this;
    }

    public function addNetwork(string $networkIdentifier): FetchInfo
    {
        $this->networkList[] = $networkIdentifier;

        return $this;
    }

    public function hasProfileList(): bool
    {
        return 0 !== count($this->profileList);
    }

    public function getProfileList(): array
    {
        return $this->profileList;
    }

    public function setProfileList(array $profileList): FetchInfo
    {
        $this->profileList = $profileList;

        return $this;
    }

    public function hasFromDateTime(): bool
    {
        return $this->fromDateTime !== null;
    }

    public function getFromDateTime(): ?\DateTime
    {
        return $this->fromDateTime;
    }

    public function setFromDateTime(?\DateTime $fromDateTime): FetchInfo
    {
        $this->fromDateTime = $fromDateTime;

        return $this;
    }

    public function hasUntilDatetime(): bool
    {
        return $this->untilDateTime !== null;
    }

    public function getUntilDateTime(): ?\DateTime
    {
        return $this->untilDateTime;
    }

    public function setUntilDateTime(?\DateTime $untilDateTime): FetchInfo
    {
        $this->untilDateTime = $untilDateTime;

        return $this;
    }

    public function isIncludeFailedProfiles(): bool
    {
        return $this->includeFailedProfiles;
    }

    public function setIncludeFailedProfiles(bool $includeFailedProfiles): FetchInfo
    {
        $this->includeFailedProfiles = $includeFailedProfiles;

        return $this;
    }

    public function isIncludeSuceededProfiles(): bool
    {
        return $this->includeSuceededProfiles;
    }

    public function setIncludeSuceededProfiles(bool $includeSuceededProfiles): FetchInfo
    {
        $this->includeSuceededProfiles = $includeSuceededProfiles;

        return $this;
    }

    public function includeOldItems(): bool
    {
        return $this->includeOldItems;
    }

    public function setIncludeOldItems(bool $includeOldItems): FetchInfo
    {
        $this->includeOldItems = $includeOldItems;

        return $this;
    }
}