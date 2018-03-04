<?php

namespace Criticalmass\Component\SocialNetwork\FeedFetcher;

use Criticalmass\Bundle\AppBundle\Entity\SocialNetworkProfile;
use Criticalmass\Component\SocialNetwork\Network\NetworkInterface;
use Criticalmass\Component\SocialNetwork\NetworkFeedFetcher\NetworkFeedFetcherInterface;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;

class FeedFetcher
{
    /** @var array $networkFetcherList */
    protected $networkFetcherList = [];

    /** @var Doctrine $doctrine */
    protected $doctrine;

    protected $feedItemList = [];

    public function __construct(Doctrine $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function addNetworkFeedFetcher(NetworkFeedFetcherInterface $networkFeedFetcher): FeedFetcher
    {
        $this->networkFetcherList[] = $networkFeedFetcher;

        return $this;
    }

    public function getNetworkFetcherList(): array
    {
        return $this->networkFetcherList;
    }

    protected function getSocialNetworkProfiles(): array
    {
        return $this->doctrine->getRepository(SocialNetworkProfile::class)->findAll();
    }

    protected function getFeedFetcherForNetworkProfile(SocialNetworkProfile $socialNetworkProfile
    ): ?NetworkFeedFetcherInterface
    {
        $namespace = 'Criticalmass\\Component\\SocialNetwork\\NetworkFeedFetcher\\';

        $network = ucfirst($socialNetworkProfile->getNetwork());

        $classname = sprintf('%s%sFeedFetcher', $namespace, $network);

        if (class_exists($classname)) {
            return new $classname;
        }

        return null;
    }

    public function fetch()
    {
        $profileList = $this->getSocialNetworkProfiles();

        foreach ($profileList as $profile) {
            $fetcher = $this->getFeedFetcherForNetworkProfile($profile);

            if ($fetcher) {
                $feedItemList = $fetcher->fetch($profile)->getFeedItemList();

                $this->feedItemList = array_merge($this->feedItemList, $feedItemList);
            }

        }

        return $this;
    }

    public function persist()
    {
        $em = $this->doctrine->getManager();

        foreach ($this->feedItemList as $feedItem) {
            $em->persist($feedItem);
        }

        return $this;
    }

    public function getFeedItemList(): array
    {
        return $this->feedItemList;
    }
}
