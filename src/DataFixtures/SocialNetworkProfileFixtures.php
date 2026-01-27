<?php declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\SocialNetworkProfile;
use App\Entity\User;
use Carbon\Carbon;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SocialNetworkProfileFixtures extends Fixture implements DependentFixtureInterface
{
    public const HAMBURG_TWITTER_REFERENCE = 'social-hamburg-twitter';
    public const HAMBURG_FACEBOOK_REFERENCE = 'social-hamburg-facebook';
    public const HAMBURG_INSTAGRAM_REFERENCE = 'social-hamburg-instagram';
    public const BERLIN_TWITTER_REFERENCE = 'social-berlin-twitter';
    public const BERLIN_MASTODON_REFERENCE = 'social-berlin-mastodon';
    public const MUNICH_INSTAGRAM_REFERENCE = 'social-munich-instagram';
    public const KIEL_FACEBOOK_REFERENCE = 'social-kiel-facebook';

    public function load(ObjectManager $manager): void
    {
        /** @var City $hamburg */
        $hamburg = $this->getReference(CityFixtures::HAMBURG_REFERENCE, City::class);
        /** @var City $berlin */
        $berlin = $this->getReference(CityFixtures::BERLIN_REFERENCE, City::class);
        /** @var City $munich */
        $munich = $this->getReference(CityFixtures::MUNICH_REFERENCE, City::class);
        /** @var City $kiel */
        $kiel = $this->getReference(CityFixtures::KIEL_REFERENCE, City::class);

        /** @var User $adminUser */
        $adminUser = $this->getReference(UserFixtures::ADMIN_USER_REFERENCE, User::class);

        $hamburgTwitter = $this->createProfile(
            $hamburg,
            $adminUser,
            'twitter',
            'criticalmassHH',
            true
        );
        $this->addReference(self::HAMBURG_TWITTER_REFERENCE, $hamburgTwitter);
        $manager->persist($hamburgTwitter);

        $hamburgFacebook = $this->createProfile(
            $hamburg,
            $adminUser,
            'facebook',
            'CriticalMassHamburg',
            false
        );
        $this->addReference(self::HAMBURG_FACEBOOK_REFERENCE, $hamburgFacebook);
        $manager->persist($hamburgFacebook);

        $hamburgInstagram = $this->createProfile(
            $hamburg,
            $adminUser,
            'instagram',
            'criticalmass_hamburg',
            false
        );
        $this->addReference(self::HAMBURG_INSTAGRAM_REFERENCE, $hamburgInstagram);
        $manager->persist($hamburgInstagram);

        $berlinTwitter = $this->createProfile(
            $berlin,
            $adminUser,
            'twitter',
            'CMBerlin',
            true
        );
        $this->addReference(self::BERLIN_TWITTER_REFERENCE, $berlinTwitter);
        $manager->persist($berlinTwitter);

        $berlinMastodon = $this->createProfile(
            $berlin,
            $adminUser,
            'mastodon',
            '@criticalmass@mastodon.social',
            false
        );
        $this->addReference(self::BERLIN_MASTODON_REFERENCE, $berlinMastodon);
        $manager->persist($berlinMastodon);

        $munichInstagram = $this->createProfile(
            $munich,
            $adminUser,
            'instagram',
            'criticalmass_munich',
            true
        );
        $this->addReference(self::MUNICH_INSTAGRAM_REFERENCE, $munichInstagram);
        $manager->persist($munichInstagram);

        $kielFacebook = $this->createProfile(
            $kiel,
            $adminUser,
            'facebook',
            'CriticalMassKiel',
            true
        );
        $this->addReference(self::KIEL_FACEBOOK_REFERENCE, $kielFacebook);
        $manager->persist($kielFacebook);

        $manager->flush();
    }

    private function createProfile(
        City $city,
        User $createdBy,
        string $network,
        string $identifier,
        bool $mainNetwork
    ): SocialNetworkProfile {
        return (new SocialNetworkProfile())
            ->setCity($city)
            ->setCreatedBy($createdBy)
            ->setNetwork($network)
            ->setIdentifier($identifier)
            ->setMainNetwork($mainNetwork)
            ->setEnabled(true)
            ->setAutoPublish(true)
            ->setAutoFetch(true)
            ->setCreatedAt(Carbon::now());
    }

    public function getDependencies(): array
    {
        return [
            CityFixtures::class,
            UserFixtures::class,
        ];
    }
}
