<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\NetworkFeedFetcher;

use App\Entity\SocialNetworkFeedItem;
use App\Entity\SocialNetworkProfile;
use Codebird\Codebird;

class TwitterFeedFetcher extends AbstractNetworkFeedFetcher
{
    /** @var string $twitterClientId */
    protected $twitterClientId;

    /** @var string $twitterSecret */
    protected $twitterSecret;

    /** @var Codebird $codebird */
    protected $codebird;

    public function __construct(string $twitterClientId, string $twitterSecret)
    {
        $this->twitterClientId = $twitterClientId;
        $this->twitterSecret = $twitterSecret;

        Codebird::setConsumerKey($this->twitterClientId, $this->twitterSecret);
        $this->codebird = Codebird::getInstance();
    }

    public function fetch(SocialNetworkProfile $socialNetworkProfile): NetworkFeedFetcherInterface
    {
        try {
            $this->fetchFeed($socialNetworkProfile);
        } catch (\Exception $exception) {
            var_dump($exception->getMessage());
        }

        return $this;
    }

    protected function fetchFeed(SocialNetworkProfile $socialNetworkProfile): NetworkFeedFetcherInterface
    {
        //$reply = $cb->oauth2_token();
        //$bearer_token = $reply->access_token;

        if (!$this->isValidScreenname($socialNetworkProfile)) {
            return $this;
        }

        $reply = $this->codebird->statuses_userTimeline(sprintf('screen_name=%s&tweet_mode=extended', $socialNetworkProfile->getIdentifier()), true);
        $data = (array) $reply;

        foreach ($data as $tweet) {
            if (!is_object($tweet)) {
                continue;
            }

            $feedItem = $this->convertEntryToFeedItem($tweet);

            if ($feedItem) {
                $feedItem->setSocialNetworkProfile($socialNetworkProfile);

                $this->feedItemList[] = $feedItem;
            }
        }

        return $this;
    }

    protected function convertEntryToFeedItem(\stdClass $tweet): ?SocialNetworkFeedItem
    {
        $feedItem = new SocialNetworkFeedItem();

        try {
            $uniqueId = $tweet->id_str;
            $permalink = sprintf('https://twitter.com/i/web/status/%s', $tweet->id);
            $text = $tweet->full_text;
            $dateTime = new \DateTime($tweet->created_at);

            if ($uniqueId && $permalink && $text && $dateTime) {
                $feedItem
                    ->setUniqueIdentifier($uniqueId)
                    ->setPermalink($permalink)
                    ->setText($text)
                    ->setDateTime($dateTime);

                return $feedItem;
            }

            return $feedItem;
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }

    protected function isValidScreenname(SocialNetworkProfile $socialNetworkProfile): bool
    {
        return preg_match('/^@?(\w){1,15}$/', $socialNetworkProfile->getIdentifier()) !== false;
    }
}
