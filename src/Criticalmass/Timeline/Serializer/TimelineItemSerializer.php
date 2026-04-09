<?php declare(strict_types=1);

namespace App\Criticalmass\Timeline\Serializer;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Criticalmass\TextParser\TextParserInterface;
use App\Criticalmass\Timeline\Item\CityCreatedItem;
use App\Criticalmass\Timeline\Item\CityEditItem;
use App\Criticalmass\Timeline\Item\ItemInterface;
use App\Criticalmass\Timeline\Item\PhotoCommentItem;
use App\Criticalmass\Timeline\Item\RideCommentItem;
use App\Criticalmass\Timeline\Item\RideEditItem;
use App\Criticalmass\Timeline\Item\RideParticipationEstimateItem;
use App\Criticalmass\Timeline\Item\RidePhotoItem;
use App\Criticalmass\Timeline\Item\RideTrackItem;
use App\Criticalmass\Timeline\Item\SocialNetworkFeedItemItem;
use App\Criticalmass\Timeline\Item\ThreadItem;
use App\Criticalmass\Timeline\Item\ThreadPostItem;
use App\Criticalmass\Timeline\Item\AbstractItem;
use Khill\Duration\Duration;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Salavert\Twig\Extension\TimeAgoExtension;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class TimelineItemSerializer
{
    public function __construct(
        private readonly ObjectRouterInterface $objectRouter,
        private readonly UploaderHelper $uploaderHelper,
        private readonly CacheManager $cacheManager,
        private readonly TextParserInterface $textParser,
        private readonly TimeAgoExtension $timeAgoExtension,
    ) {
    }

    /** @return array<string, mixed> */
    public function serialize(ItemInterface $item): array
    {
        $data = $this->serializeBase($item);

        $data = match (true) {
            $item instanceof CityCreatedItem => array_merge($data, $this->serializeCityCreated($item)),
            $item instanceof CityEditItem => array_merge($data, $this->serializeCityEdit($item)),
            $item instanceof RideCommentItem => array_merge($data, $this->serializeRideComment($item)),
            $item instanceof RideEditItem => array_merge($data, $this->serializeRideEdit($item)),
            $item instanceof RidePhotoItem => array_merge($data, $this->serializeRidePhoto($item)),
            $item instanceof PhotoCommentItem => array_merge($data, $this->serializePhotoComment($item)),
            $item instanceof RideParticipationEstimateItem => array_merge($data, $this->serializeRideParticipationEstimate($item)),
            $item instanceof RideTrackItem => array_merge($data, $this->serializeRideTrack($item)),
            $item instanceof ThreadItem => array_merge($data, $this->serializeThread($item)),
            $item instanceof ThreadPostItem => array_merge($data, $this->serializeThreadPost($item)),
            $item instanceof SocialNetworkFeedItemItem => array_merge($data, $this->serializeSocialNetworkFeed($item)),
            default => $data,
        };

        return $data;
    }

    /** @return array<string, mixed> */
    private function serializeBase(ItemInterface $item): array
    {
        $type = $this->getTypeFromItem($item);

        $data = [
            'type' => $type,
            'uniqId' => $item->getUniqId(),
            'dateTime' => $item->getDateTime()->format('c'),
            'dateTimeFormatted' => $item->getDateTime()->format('d.m.Y H:i'),
            'timeAgo' => $this->timeAgoExtension->timeAgoInWordsFilter($item->getDateTime()),
        ];

        if ($item instanceof AbstractItem && $item->getUser() !== null) {
            $user = $item->getUser();
            $avatarUrl = null;

            if ($user->getImageName()) {
                $imageFilename = $this->uploaderHelper->asset($user, 'imageFile');

                if ($imageFilename) {
                    $avatarUrl = $this->cacheManager->getBrowserPath($imageFilename, 'user_profile_photo_small');
                }
            }

            $data['user'] = [
                'username' => $user->getUsername(),
                'avatarUrl' => $avatarUrl,
            ];
        }

        return $data;
    }

    private function getTypeFromItem(ItemInterface $item): string
    {
        $className = (new \ReflectionClass($item))->getShortName();
        $type = str_replace('Item', '', $className);

        return lcfirst($type);
    }

    /** @return array<string, mixed> */
    private function serializeCityCreated(CityCreatedItem $item): array
    {
        $city = $item->getCity();

        return [
            'cityName' => $item->getCityName(),
            'cityUrl' => $this->objectRouter->generate($city),
            'latitude' => $city->getLatitude(),
            'longitude' => $city->getLongitude(),
        ];
    }

    /** @return array<string, mixed> */
    private function serializeCityEdit(CityEditItem $item): array
    {
        $city = $item->getCity();

        return [
            'cityName' => $item->getCityName(),
            'cityUrl' => $this->objectRouter->generate($city),
            'latitude' => $city->getLatitude(),
            'longitude' => $city->getLongitude(),
        ];
    }

    /** @return array<string, mixed> */
    private function serializeRideComment(RideCommentItem $item): array
    {
        $ride = $item->getRide();
        $post = $item->getPost();

        return [
            'rideTitle' => $item->getRideTitle(),
            'rideUrl' => $this->objectRouter->generate($ride),
            'rideEnabled' => $item->isRideEnabled(),
            'postId' => $post->getId(),
            'message' => $post->getMessage(),
        ];
    }

    /** @return array<string, mixed> */
    private function serializeRideEdit(RideEditItem $item): array
    {
        $ride = $item->getRide();

        return [
            'rideTitle' => $item->getRideTitle(),
            'rideUrl' => $this->objectRouter->generate($ride),
            'rideEnabled' => $item->isEnabled(),
            'latitude' => $ride->getLatitude(),
            'longitude' => $ride->getLongitude(),
        ];
    }

    /** @return array<string, mixed> */
    private function serializeRidePhoto(RidePhotoItem $item): array
    {
        $ride = $item->getRide();
        $previewPhotos = [];

        foreach ($item->getPreviewPhotoList() as $photo) {
            $imageFilename = $this->uploaderHelper->asset($photo, 'imageFile');
            $imageUrl = null;

            if ($imageFilename) {
                $imageUrl = $this->cacheManager->getBrowserPath($imageFilename, 'gallery_photo_thumb');
            }

            $previewPhotos[] = [
                'imageUrl' => $imageUrl,
                'photoUrl' => $this->objectRouter->generate($photo),
            ];
        }

        return [
            'rideTitle' => $ride->getTitle(),
            'rideUrl' => $this->objectRouter->generate($ride),
            'rideEnabled' => $item->isRideEnabled(),
            'photoCount' => $item->getCounter(),
            'photoGalleryUrl' => $this->objectRouter->generate($ride, 'caldera_criticalmass_photo_ride_list'),
            'previewPhotos' => $previewPhotos,
        ];
    }

    /** @return array<string, mixed> */
    private function serializePhotoComment(PhotoCommentItem $item): array
    {
        $ride = $item->getRide();
        $posts = [];

        foreach ($item->getPostList() as $post) {
            $photo = $post->getPhoto();
            $imageFilename = $this->uploaderHelper->asset($photo, 'imageFile');
            $imageUrl = null;

            if ($imageFilename) {
                $imageUrl = $this->cacheManager->getBrowserPath($imageFilename, 'gallery_photo_thumb');
            }

            $posts[] = [
                'imageUrl' => $imageUrl,
                'photoUrl' => $this->objectRouter->generate($photo) . '#post-' . $post->getId(),
                'messageHtml' => $this->textParser->parse($post->getMessage()),
            ];
        }

        return [
            'rideTitle' => $ride->getTitle(),
            'rideUrl' => $this->objectRouter->generate($ride),
            'postCount' => count($item->getPostList()),
            'posts' => $posts,
        ];
    }

    /** @return array<string, mixed> */
    private function serializeRideParticipationEstimate(RideParticipationEstimateItem $item): array
    {
        $ride = $item->getRide();
        $participants = $item->getEstimatedParticipants();

        $counterString = str_pad((string) $participants, 4, '0', STR_PAD_LEFT);
        $counterDigits = str_split($counterString);

        return [
            'rideTitle' => $item->getRideTitle(),
            'rideUrl' => $this->objectRouter->generate($ride),
            'rideEnabled' => $item->isRideEnabled(),
            'estimatedParticipants' => $participants,
            'counterDigits' => $counterDigits,
        ];
    }

    /** @return array<string, mixed> */
    private function serializeRideTrack(RideTrackItem $item): array
    {
        $ride = $item->getRide();
        $track = $item->getTrack();
        $distance = $item->getDistance();
        $duration = $item->getDuration();

        $durationFormatted = null;
        if ($duration) {
            $durationFormatted = (new Duration())->humanize((string) $duration);
        }

        return [
            'rideTitle' => $item->getRideTitle(),
            'rideUrl' => $this->objectRouter->generate($ride),
            'rideEnabled' => $item->isRideEnabled(),
            'rideDate' => $ride->getDateTime()->format('d.m.Y'),
            'distanceFormatted' => number_format($distance, 2, ',', '.') . "\u{00a0}km",
            'durationFormatted' => $durationFormatted,
            'polyline' => $item->getPolyline(),
            'polylineColor' => $track->getUser()->getColor(),
            'latitude' => $ride->getLatitude(),
            'longitude' => $ride->getLongitude(),
        ];
    }

    /** @return array<string, mixed> */
    private function serializeThread(ThreadItem $item): array
    {
        $thread = $item->getThread();
        $text = $item->getText();
        $textHtml = $this->textParser->parse(mb_substr($text, 0, 140));

        return [
            'title' => $item->getTitle(),
            'threadUrl' => $this->objectRouter->generate($thread),
            'textHtml' => $textHtml,
        ];
    }

    /** @return array<string, mixed> */
    private function serializeThreadPost(ThreadPostItem $item): array
    {
        $thread = $item->getThread();
        $text = $item->getText();
        $textHtml = $this->textParser->parse(mb_substr($text, 0, 140));

        return [
            'threadTitle' => $item->getThreadTitle(),
            'threadUrl' => $this->objectRouter->generate($thread) . '#post-' . $item->getPostId(),
            'postId' => $item->getPostId(),
            'textHtml' => $textHtml,
        ];
    }

    /** @return array<string, mixed> */
    private function serializeSocialNetworkFeed(SocialNetworkFeedItemItem $item): array
    {
        $feedItem = $item->getSocialNetworkFeedItem();
        $profile = $feedItem->getSocialNetworkProfile();
        $network = $profile->getNetwork();

        $city = $profile->getCity();
        if ($city === null && $profile->getRide() !== null) {
            $city = $profile->getRide()->getCity();
        }

        $text = $feedItem->getText();
        $textTrimmed = $this->trimIntro($text);

        return [
            'network' => $network,
            'cityName' => $city ? $city->getTitle() : null,
            'cityUrl' => $city ? $this->objectRouter->generate($city) : null,
            'text' => $network === 'mastodon' ? strip_tags($text) : $text,
            'textTrimmed' => $textTrimmed,
            'title' => $feedItem->getTitle(),
            'permalink' => $feedItem->getPermalink(),
        ];
    }

    private function trimIntro(string $text): string
    {
        $text = strip_tags($text);
        $textLength = strlen($text);

        if ($textLength > 350) {
            $additionalLength = 350;

            while ($additionalLength < $textLength - 1) {
                ++$additionalLength;

                if (in_array($text[$additionalLength], ['.', ';', '!', '?', "\u{2026}"])) {
                    break;
                }
            }

            return substr($text, 0, $additionalLength + 1);
        }

        return $text;
    }
}
