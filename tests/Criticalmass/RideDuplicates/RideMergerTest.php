<?php declare(strict_types=1);

namespace Tests\Criticalmass\RideDuplicates;

use App\Criticalmass\RideDuplicates\RideMerger\RideMerger;
use App\Entity\Photo;
use App\Entity\Post;
use App\Entity\Ride;
use App\Entity\RideEstimate;
use App\Entity\SocialNetworkProfile;
use App\Entity\Subride;
use App\Entity\Track;
use App\Entity\Weather;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Support\EntityIdHelper;

final class RideMergerTest extends TestCase
{
    private function ride(int $id): Ride
    {
        $ride = new Ride();
        EntityIdHelper::setId($ride, $id);

        return $ride;
    }

    #[Test]
    public function movesAllRelationsFromSourceRidesToTheTarget(): void
    {
        $target = $this->ride(1);
        $source = $this->ride(2);

        $track = (new Track())->setRide($source);
        $photo = (new Photo())->setRide($source);
        $post = (new Post())->setRide($source);
        $subride = (new Subride())->setRide($source);
        $estimate = (new RideEstimate())->setRide($source);
        $weather = (new Weather())->setRide($source);
        $profile = (new SocialNetworkProfile())->setRide($source);

        $source
            ->addTrack($track)
            ->addPhoto($photo)
            ->addPost($post)
            ->addSubride($subride)
            ->addEstimate($estimate)
            ->addWeather($weather)
            ->addSocialNetworkProfile($profile);

        $merged = (new RideMerger())->setTargetRide($target)->addSourceRide($source)->merge();

        self::assertSame($target, $merged);

        self::assertSame([$track], $target->getTracks()->toArray());
        self::assertSame([$photo], $target->getPhotos()->toArray());
        self::assertSame([$post], $target->getPosts()->toArray());
        self::assertSame([$subride], $target->getSubrides()->toArray());
        self::assertSame([$estimate], $target->getEstimates()->toArray());
        self::assertSame([$weather], $target->getWeathers()->toArray());
        self::assertSame([$profile], $target->getSocialNetworkProfiles()->toArray());

        foreach ([$track, $photo, $post, $subride, $estimate, $weather, $profile] as $relation) {
            self::assertSame($target, $relation->getRide());
        }

        self::assertCount(0, $source->getTracks());
        self::assertCount(0, $source->getPhotos());
        self::assertCount(0, $source->getPosts());
    }

    #[Test]
    public function sourceRidesAreDeduplicatedById(): void
    {
        $target = $this->ride(1);
        $source = $this->ride(2);
        $source->addTrack((new Track())->setRide($source));

        $merged = (new RideMerger())
            ->setTargetRide($target)
            ->addSourceRides([$source, $source])
            ->addSourceRide($source)
            ->merge();

        self::assertCount(1, $merged->getTracks());
    }

    #[Test]
    public function mergingSeveralSourcesKeepsExistingTargetRelations(): void
    {
        $target = $this->ride(1);
        $ownTrack = (new Track())->setRide($target);
        $target->addTrack($ownTrack);

        $a = $this->ride(2);
        $a->addTrack((new Track())->setRide($a));
        $b = $this->ride(3);
        $b->addTrack((new Track())->setRide($b));

        $merged = (new RideMerger())->setTargetRide($target)->addSourceRides([$a, $b])->merge();

        self::assertCount(3, $merged->getTracks());
        self::assertContains($ownTrack, $merged->getTracks()->toArray());
    }
}
