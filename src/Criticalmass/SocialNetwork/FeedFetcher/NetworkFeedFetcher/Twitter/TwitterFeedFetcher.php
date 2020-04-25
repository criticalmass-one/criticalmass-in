<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\Twitter;

use App\Criticalmass\SocialNetwork\FeedFetcher\FetchInfo;
use App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\AbstractNetworkFeedFetcher;
use App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\NetworkFeedFetcherInterface;
use App\Entity\SocialNetworkFeedItem;
use App\Entity\SocialNetworkProfile;
use Codebird\Codebird;
use Psr\Log\LoggerInterface;

class TwitterFeedFetcher extends AbstractNetworkFeedFetcher
{
    protected Codebird $codebird;

    public function __construct(LoggerInterface $logger, string $twitterClientId, string $twitterSecret)
    {
        Codebird::setConsumerKey($twitterClientId, $twitterSecret);
        $this->codebird = Codebird::getInstance();

        parent::__construct($logger);
    }

    public function fetch(SocialNetworkProfile $socialNetworkProfile, FetchInfo $fetchInfo): NetworkFeedFetcherInterface
    {
        if (!$socialNetworkProfile->getCity()) {
            return $this;
        }

        try {
            $this->fetchFeed($socialNetworkProfile, $fetchInfo);
        } catch (\Exception $exception) {
            $this->markAsFailed($socialNetworkProfile, sprintf('Failed to fetch social network profile %d: %s', $socialNetworkProfile->getId(), $exception->getMessage()));
        }

        return $this;
    }

    protected function fetchFeed(SocialNetworkProfile $socialNetworkProfile, FetchInfo $fetchInfo): NetworkFeedFetcherInterface
    {
        $screenname = Screenname::extractScreenname($socialNetworkProfile);

        if (!$screenname || !Screenname::isValidScreenname($screenname)) {
            $this->markAsFailed($socialNetworkProfile, sprintf('Skipping %s cause it is not a valid twitter handle.', $screenname));

            return $this;
        }

        $this->logger->info(sprintf('Now quering @%s', $screenname));

        $reply = $this->codebird->statuses_userTimeline(QueryBuilder::build($socialNetworkProfile, $fetchInfo), true);
        $data = (array)$reply;

        foreach ($data as $tweet) {
            if (!is_object($tweet)) {
                $this->logger->info('Tweet did not contain usable data. Skipping.');

                continue;
            }

            $feedItem = $this->convertEntryToFeedItem($tweet);

            if ($feedItem) {
                $feedItem->setSocialNetworkProfile($socialNetworkProfile);

                $this->logger->info(sprintf('Parsed and added tweet #%s', $feedItem->getUniqueIdentifier()));

                $this->feedItemList[] = $feedItem;
            }
        }

        $socialNetworkProfile->setLastFetchSuccessDateTime(new \DateTime());

        return $this;
    }

    protected function convertEntryToFeedItem(\stdClass $tweet): ?SocialNetworkFeedItem
    {
        $feedItem = new SocialNetworkFeedItem();

        try {
            $permalink = sprintf('https://twitter.com/i/web/status/%s', $tweet->id);
            $text = $tweet->full_text;
            $dateTime = new \DateTime($tweet->created_at);

            if ($permalink && $text && $dateTime) {
                $feedItem
                    ->setUniqueIdentifier($permalink)
                    ->setPermalink($permalink)
                    ->setText($text)
                    ->setDateTime($dateTime);

                return $feedItem;
            }

            return $feedItem;
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function markAsFailed(SocialNetworkProfile $socialNetworkProfile, string $errorMessage): SocialNetworkProfile
    {
        $socialNetworkProfile
            ->setLastFetchFailureDateTime(new \DateTime())
            ->setLastFetchFailureError($errorMessage);

        $this
            ->logger
            ->notice($errorMessage);

        return $socialNetworkProfile;
    }
}
