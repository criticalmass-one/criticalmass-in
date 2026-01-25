<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TrackFixtures extends Fixture implements DependentFixtureInterface
{
    public const HAMBURG_TRACK_1_REFERENCE = 'track-hamburg-1';
    public const HAMBURG_TRACK_2_REFERENCE = 'track-hamburg-2';
    public const BERLIN_TRACK_1_REFERENCE = 'track-berlin-1';
    public const MUNICH_TRACK_1_REFERENCE = 'track-munich-1';

    public function load(ObjectManager $manager): void
    {
        /** @var User $regularUser */
        $regularUser = $this->getReference(UserFixtures::REGULAR_USER_REFERENCE, User::class);
        /** @var User $cyclistUser */
        $cyclistUser = $this->getReference(UserFixtures::CYCLIST_USER_REFERENCE, User::class);

        /** @var Ride $hamburgRidePast */
        $hamburgRidePast = $this->getReference(RideFixtures::HAMBURG_RIDE_PAST_REFERENCE, Ride::class);
        /** @var Ride $berlinRidePast */
        $berlinRidePast = $this->getReference(RideFixtures::BERLIN_RIDE_PAST_REFERENCE, Ride::class);
        /** @var Ride $munichRidePast */
        $munichRidePast = $this->getReference(RideFixtures::MUNICH_RIDE_PAST_REFERENCE, Ride::class);

        $hamburgTrack1 = $this->createTrack(
            $hamburgRidePast,
            $regularUser,
            'testuser',
            Track::TRACK_SOURCE_GPX,
            15.5,
            2.5,
            1250
        );
        $this->addReference(self::HAMBURG_TRACK_1_REFERENCE, $hamburgTrack1);
        $manager->persist($hamburgTrack1);

        $hamburgTrack2 = $this->createTrack(
            $hamburgRidePast,
            $cyclistUser,
            'cyclist',
            Track::TRACK_SOURCE_STRAVA,
            16.2,
            2.75,
            1380
        );
        $this->addReference(self::HAMBURG_TRACK_2_REFERENCE, $hamburgTrack2);
        $manager->persist($hamburgTrack2);

        $berlinTrack1 = $this->createTrack(
            $berlinRidePast,
            $regularUser,
            'testuser',
            Track::TRACK_SOURCE_GPX,
            18.0,
            3.0,
            1520
        );
        $this->addReference(self::BERLIN_TRACK_1_REFERENCE, $berlinTrack1);
        $manager->persist($berlinTrack1);

        $munichTrack1 = $this->createTrack(
            $munichRidePast,
            $cyclistUser,
            'cyclist',
            Track::TRACK_SOURCE_CRITICALMAPS,
            12.5,
            2.0,
            980
        );
        $this->addReference(self::MUNICH_TRACK_1_REFERENCE, $munichTrack1);
        $manager->persist($munichTrack1);

        $manager->flush();
    }

    private function createTrack(
        Ride $ride,
        User $user,
        string $username,
        string $source,
        float $distance,
        float $durationHours,
        int $points
    ): Track {
        $startDateTime = clone $ride->getDateTime();
        $endDateTime = clone $startDateTime;
        $endDateTime->modify('+' . (int)($durationHours * 60) . ' minutes');

        $track = new Track();
        $track
            ->setRide($ride)
            ->setUser($user)
            ->setUsername($username)
            ->setSource($source)
            ->setDistance($distance)
            ->setPoints($points)
            ->setStartDateTime($startDateTime)
            ->setEndDateTime($endDateTime)
            ->setCreationDateTime(new \DateTime())
            ->setEnabled(true)
            ->setDeleted(false)
            ->setReviewed(true)
            ->setPolyline($this->generateSamplePolyline($ride))
            ->setMd5Hash(md5($ride->getId() . $user->getId() . time()));

        return $track;
    }

    private function generateSamplePolyline(Ride $ride): string
    {
        $lat = $ride->getLatitude();
        $lng = $ride->getLongitude();

        $points = [];
        for ($i = 0; $i < 10; $i++) {
            $points[] = [
                $lat + ($i * 0.001),
                $lng + ($i * 0.001),
            ];
        }

        return $this->encodePolyline($points);
    }

    private function encodePolyline(array $points): string
    {
        $encodedString = '';
        $prevLat = 0;
        $prevLng = 0;

        foreach ($points as $point) {
            $lat = (int)round($point[0] * 1e5);
            $lng = (int)round($point[1] * 1e5);

            $dLat = $lat - $prevLat;
            $dLng = $lng - $prevLng;

            $prevLat = $lat;
            $prevLng = $lng;

            $encodedString .= $this->encodeNumber($dLat);
            $encodedString .= $this->encodeNumber($dLng);
        }

        return $encodedString;
    }

    private function encodeNumber(int $num): string
    {
        $sgn_num = $num << 1;
        if ($num < 0) {
            $sgn_num = ~$sgn_num;
        }

        $encoded = '';
        while ($sgn_num >= 0x20) {
            $encoded .= chr((0x20 | ($sgn_num & 0x1f)) + 63);
            $sgn_num >>= 5;
        }
        $encoded .= chr($sgn_num + 63);

        return $encoded;
    }

    public function getDependencies(): array
    {
        return [
            RideFixtures::class,
            UserFixtures::class,
        ];
    }
}
