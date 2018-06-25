<?php declare(strict_types=1);

namespace AppBundle\Command\SocialNetwork;

use AppBundle\Entity\City;
use AppBundle\Entity\Ride;
use AppBundle\Entity\SocialNetworkProfile;
use AppBundle\Entity\Subride;
use AppBundle\Criticalmass\SocialNetwork\EntityInterface\SocialNetworkProfileAble;
use AppBundle\Criticalmass\Util\ClassUtil;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertNetworkCommand extends Command
{
    /** @var Doctrine $doctrine */
    protected $doctrine;

    public function __construct(Doctrine $doctrine)
    {
        $this->doctrine = $doctrine;

        parent::__construct(null);

    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:social-network:convert')
            ->setDescription('Convert networks');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $cities = $this->doctrine->getRepository(City::class)->findAll();

        /** @var City $city */
        foreach ($cities as $city) {
            $this->checkNetworks($city);
        }

        $rides = $this->doctrine->getRepository(Ride::class)->findAll();

        /** @var Ride $ride */
        foreach ($rides as $ride) {
            $this->checkNetworks($ride);
        }

        $subrides = $this->doctrine->getRepository(Subride::class)->findAll();

        /** @var Subride $subride */
        foreach ($subrides as $subride) {
            $this->checkNetworks($subride);
        }

        $this->doctrine->getManager()->flush();
    }

    protected function checkNetworks(SocialNetworkProfileAble $profileAble): void
    {
        $networks = ['twitter' => 'twitter', 'facebook' => 'facebook_page', 'url' => 'homepage'];

        foreach ($networks as $oldProperty => $network) {
            $oldGetMethodName = sprintf('get%s', ucfirst($oldProperty));

            if ($profileAble->$oldGetMethodName()) {
                $this->createProfile($profileAble, $network, $profileAble->$oldGetMethodName());
            }
        }
    }

    protected function createProfile(SocialNetworkProfileAble $profileAble, string $network, string $identifier): SocialNetworkProfile
    {
        $setProfileableMethodName = sprintf('set%s', ClassUtil::getShortname($profileAble));

        $profile = new SocialNetworkProfile();

        $profile
            ->setIdentifier($identifier)
            ->$setProfileableMethodName($profileAble)
            ->setNetwork($network);

        $this->doctrine->getManager()->persist($profile);

        return $profile;
    }
}
