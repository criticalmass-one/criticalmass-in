<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\NetworkFeedFetcher;

use App\Entity\SocialNetworkFeedItem;
use App\Entity\SocialNetworkProfile;
use Codebird\Codebird;
use Psr\Log\LoggerInterface;

class TwitterFeedFetcher extends AbstractNetworkFeedFetcher
{
    /** @var Codebird $codebird */
    protected $codebird;

    public function __construct(LoggerInterface $logger, string $twitterClientId, string $twitterSecret)
    {
        Codebird::setConsumerKey($twitterClientId, $twitterSecret);
        $this->codebird = Codebird::getInstance();

        parent::__construct($logger);
    }

    public function fetch(SocialNetworkProfile $socialNetworkProfile): NetworkFeedFetcherInterface
    {
        if (!$socialNetworkProfile->getCity()) {
            return $this;
        }

        try {
            $this->fetchFeed($socialNetworkProfile);
        } catch (\Exception $exception) {
            $this->logger->error(sprintf('Failed to fetch social network profile %d: %s', $socialNetworkProfile->getId(), $exception->getMessage()));
        }

        return $this;
    }

    protected function fetchFeed(SocialNetworkProfile $socialNetworkProfile): NetworkFeedFetcherInterface
    {
        //$reply = $cb->oauth2_token();
        //$bearer_token = $reply->access_token;

        $screenname = $this->getScreenname($socialNetworkProfile);

        if (!$this->isValidScreenname($screenname)) {
            $this->logger->error(sprintf('Skipping %s cause it is not a valid twitter handle.', $screenname));

            return $this;
        }

        $this->logger->info(sprintf('Now quering @%s', $screenname));

        $reply = $this->codebird->statuses_userTimeline(sprintf('screen_name=%s&tweet_mode=extended', $screenname), true);
        $data = (array) $reply;

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

    protected function getScreenname(SocialNetworkProfile $socialNetworkProfile): ?string
    {
        $identifierParts = explode('/', $socialNetworkProfile->getIdentifier());

        return array_pop($identifierParts);
    }

    protected function isValidScreenname(string $screenname): bool
    {
        return (bool) preg_match('/^@?(\w){1,15}$/', $screenname);
    }
}
