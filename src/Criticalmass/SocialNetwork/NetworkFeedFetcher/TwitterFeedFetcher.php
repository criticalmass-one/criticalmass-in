<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\NetworkFeedFetcher;

use App\Entity\SocialNetworkFeedItem;
use App\Entity\SocialNetworkProfile;
use Codebird\Codebird;
use Zend\Feed\Reader\Entry\EntryInterface;
use Zend\Feed\Reader\Reader;

class TwitterFeedFetcher extends AbstractNetworkFeedFetcher
{
    public function __construct()
    {

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
        Codebird::setConsumerKey('foo', 'bar');
        $cb = Codebird::getInstance();

        $reply = $cb->statuses_userTimeline('screen_name=maltehuebner');
        $data = (array) $reply;

        echo "FOO";
        var_dump($data);
        return $this;
    }

    protected function convertEntryToFeedItem(EntryInterface $entry): ?SocialNetworkFeedItem
    {
        $feedItem = new SocialNetworkFeedItem();

        try {
            $uniqueId = $entry->getId();
            $permalink = $entry->getPermalink();
            $title = $entry->getTitle();
            $text = $entry->getContent();
            $dateTime = $entry->getDateCreated();

            if ($uniqueId && $permalink && $title && $text && $dateTime) {
                $feedItem
                    ->setUniqueIdentifier($uniqueId)
                    ->setPermalink($permalink)
                    ->setTitle($title)
                    ->setText($text)
                    ->setDateTime($dateTime);

                return $feedItem;
            }
        } catch (\Exception $e) {
            return null;
        }

        return null;
    }
}
