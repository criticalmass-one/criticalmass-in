<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher;

use App\Entity\SocialNetworkFeedItem;
use App\Entity\SocialNetworkProfile;
use App\Criticalmass\SocialNetwork\NetworkFeedFetcher\NetworkFeedFetcherInterface;
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

    protected function getFeedFetcherForNetworkProfile(SocialNetworkProfile $socialNetworkProfile): ?NetworkFeedFetcherInterface
    {
        $namespace = 'Criticalmass\\Component\\SocialNetwork\\NetworkFeedFetcher\\';

        $network = ucfirst($socialNetworkProfile->getNetwork());

        $classname = sprintf('%s%sFeedFetcher', $namespace, $network);

        if (class_exists($classname)) {
            return new $classname;
        }

        return null;
    }

    public function fetch(): FeedFetcher
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

    public function persist(): FeedFetcher
    {
        $em = $this->doctrine->getManager();

        foreach ($this->feedItemList as $feedItem) {
            if (!$this->feedItemExists($feedItem)) {
                $em->persist($feedItem);
            }
        }

        try {
            $em->flush();
        } catch (\Exception $exception) {

        }

        return $this;
    }

    protected function feedItemExists(SocialNetworkFeedItem $feedItem): bool
    {
        $existingItem = $this->doctrine->getRepository(SocialNetworkFeedItem::class)->findOneBy([
            'socialNetworkProfile' => $feedItem->getSocialNetworkProfile(),
            'uniqueIdentifier' => $feedItem->getUniqueIdentifier()
        ]);

        return $existingItem !== null;
    }

    public function getFeedItemList(): array
    {
        return $this->feedItemList;
    }
}
