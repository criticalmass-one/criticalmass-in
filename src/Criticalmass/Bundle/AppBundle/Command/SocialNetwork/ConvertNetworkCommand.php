<?php

namespace Criticalmass\Bundle\AppBundle\Command\SocialNetwork;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\SocialNetworkProfile;
use Criticalmass\Component\SocialNetwork\FeedFetcher\FeedFetcher;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Doctrine\ORM\EntityManagerInterface;
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

        $this->doctrine->getManager()->flush();
    }

    protected function checkNetworks(City $city): void
    {
        $networks = ['twitter' => 'twitter', 'facebook' => 'facebook', 'url' => 'website'];

        foreach ($networks as $oldProperty => $network) {
            $oldGetMethodName = sprintf('get%s', ucfirst($oldProperty));

            if ($city->$oldGetMethodName()) {
                $this->createProfile($city, $network, $city->$oldGetMethodName());
            }
        }
    }

    protected function createProfile(City $city, string $network, string $identifier): SocialNetworkProfile
    {
        $profile = new SocialNetworkProfile();

        $profile
            ->setIdentifier($identifier)
            ->setCity($city)
            ->setNetwork($network);

        $this->doctrine->getManager()->persist($profile);

        return $profile;
    }
}
