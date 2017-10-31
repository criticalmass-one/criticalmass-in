<?php

namespace AppBundle\Command;

use AppBundle\Entity\City;
use AppBundle\Facebook\FacebookPageApi;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Persistence\ObjectManager;
use Facebook\Facebook;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FacebookPagePropertiesCommand extends ContainerAwareCommand
{
    /** @var Registry $doctrine */
    protected $doctrine;

    /** @var ObjectManager $manager */
    protected $manager;

    /** @var Facebook $facebook */
    protected $facebook;

    protected function configure()
    {
        $this
            ->setName('criticalmass:facebook:pageproperties')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->manager = $this->doctrine->getManager();

        /** @var FacebookPageApi $fpa */
        $fpa = $this->getContainer()->get('caldera.criticalmass.facebookapi.citypageproperties');

        $cities = $this->doctrine->getRepository('AppBundle:City')->findCitiesWithFacebook();

        foreach ($cities as $city) {
            $output->writeln('Looking up ' . $city->getCity());

        /** @var City $city */
            $pageId = $this->getPageId($city);

            if ($pageId) {
                $output->writeln('Page ID is: ' . $pageId);

                $properties = $fpa->getPagePropertiesForCity($city);

                if ($properties) {
                    $this->manager->persist($properties);

                    $output->writeln('Saved properties');
                    $output->writeln('');
                }
            }
        }

        $this->manager->flush();
    }

    protected function getPageId(City $city): ?string
    {
        $facebook = $city->getFacebook();

        if (strpos($facebook, 'https://www.facebook.com/') == 0) {
            $facebook = rtrim($facebook, "/");

            $parts = explode('/', $facebook);

            $pageId = array_pop($parts);

            return $pageId;
        }

        return null;
    }

}