<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\CityActivity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CityActivityFixtures extends Fixture implements DependentFixtureInterface
{
    public const HAMBURG_ACTIVITY_REFERENCE = 'city-activity-hamburg';
    public const BERLIN_ACTIVITY_REFERENCE = 'city-activity-berlin';

    public function load(ObjectManager $manager): void
    {
        /** @var City $hamburg */
        $hamburg = $this->getReference(CityFixtures::HAMBURG_REFERENCE, City::class);
        /** @var City $berlin */
        $berlin = $this->getReference(CityFixtures::BERLIN_REFERENCE, City::class);

        $hamburgActivity = $this->createCityActivity(
            $hamburg,
            0.85,
            0.80, 42,
            0.90, 51,
            0.75, 9,
            0.85, 30
        );
        $this->addReference(self::HAMBURG_ACTIVITY_REFERENCE, $hamburgActivity);
        $manager->persist($hamburgActivity);

        $berlinActivity = $this->createCityActivity(
            $berlin,
            0.92,
            0.95, 120,
            0.88, 85,
            0.90, 25,
            0.92, 60
        );
        $this->addReference(self::BERLIN_ACTIVITY_REFERENCE, $berlinActivity);
        $manager->persist($berlinActivity);

        $manager->flush();
    }

    private function createCityActivity(
        City $city,
        float $score,
        float $participationScore,
        int $participationRawCount,
        float $photoScore,
        int $photoRawCount,
        float $trackScore,
        int $trackRawCount,
        float $socialFeedScore,
        int $socialFeedRawCount
    ): CityActivity {
        return (new CityActivity())
            ->setCity($city)
            ->setScore($score)
            ->setParticipationScore($participationScore)
            ->setParticipationRawCount($participationRawCount)
            ->setPhotoScore($photoScore)
            ->setPhotoRawCount($photoRawCount)
            ->setTrackScore($trackScore)
            ->setTrackRawCount($trackRawCount)
            ->setSocialFeedScore($socialFeedScore)
            ->setSocialFeedRawCount($socialFeedRawCount);
    }

    public function getDependencies(): array
    {
        return [
            CityFixtures::class,
        ];
    }
}
