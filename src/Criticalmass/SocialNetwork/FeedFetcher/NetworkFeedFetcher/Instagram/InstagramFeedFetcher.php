<?php declare(strict_types=1);

namespace App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\Instagram;

use App\Criticalmass\SocialNetwork\FeedFetcher\FetchInfo;
use App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\AbstractNetworkFeedFetcher;
use App\Criticalmass\SocialNetwork\FeedFetcher\NetworkFeedFetcher\NetworkFeedFetcherInterface;
use App\Entity\SocialNetworkProfile;
use InstagramScraper\Exception\InstagramNotFoundException;
use InstagramScraper\Instagram;
use InstagramScraper\Model\Media;
use Psr\Log\LoggerInterface;

class InstagramFeedFetcher extends AbstractNetworkFeedFetcher
{
    protected Instagram $instagram;

    public function __construct(LoggerInterface $logger)
    {
        $this->instagram = new \InstagramScraper\Instagram();

        parent::__construct($logger);
    }

    public function fetch(SocialNetworkProfile $socialNetworkProfile, FetchInfo $fetchInfo): NetworkFeedFetcherInterface
    {
        $username = Screenname::extractScreenname($socialNetworkProfile);

        if (!$username || !Screenname::isValidScreenname($username)) {
            $this->markAsFailed($socialNetworkProfile, sprintf('Skipping %s cause it is not a valid instagram username.', $username));
        }

        $this->logger->info(sprintf('Now quering @%s', $username));

        $additionalData = $socialNetworkProfile->getAdditionalData();

        /*        if (array_key_exists('lastMediaId', $additionalData)) {
                    $lastFetchedMediaId = $additionalData['lastMediaId'];
                } else {
                    $lastFetchedMediaId = '';
                }*/

        // @todo fix last media id somehow
        $lastFetchedMediaId = '';
        
        try {
            $mediaList = $this->instagram->getMedias($username, 100, $lastFetchedMediaId);
        } catch (InstagramNotFoundException $exception) {
            $this->markAsFailed($socialNetworkProfile, $exception->getMessage());
        }

        if (!isset($mediaList) || 0 === count($mediaList)) {
            return $this;
        }

        $lastMediaId = null;

        /** @var Media $media */
        foreach ($mediaList as $media) {
            if (!$lastMediaId || $lastMediaId < $media->getId()) {
                $lastMediaId = $media->getId();
            }

            $feedItem = MediaConverter::convert($socialNetworkProfile, $media);

            if ($feedItem) {
                $this->logger->info(sprintf('Parsed and added instagram photo #%s', $feedItem->getUniqueIdentifier()));

                $this->feedItemList[] = $feedItem;

                if ($lastMediaId) {
                    $socialNetworkProfile->setAdditionalData(['lastMediaId' => $lastMediaId]);
                }
            }
        }

        return $this;
    }

    public function getNetworkIdentifier(): string
    {
        return 'instagram_profile';
    }
}