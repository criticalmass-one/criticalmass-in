<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\CitySlug;
use Caldera\Bundle\CalderaBundle\Entity\Ticket;
use Caldera\Bundle\CriticalmassCoreBundle\Glympse\Exception\GlympseApiBrokenException;
use Caldera\Bundle\CriticalmassCoreBundle\Glympse\Exception\GlympseApiErrorException;
use Caldera\Bundle\CriticalmassCoreBundle\Glympse\Exception\GlympseException;
use Caldera\Bundle\CriticalmassCoreBundle\Glympse\Exception\GlympseInviteUnknownException;
use Caldera\Bundle\CriticalmassCoreBundle\Statistic\RideEstimate\RideEstimateService;
use Curl\Curl;
use Doctrine\ORM\EntityManager;
use PhpImap\IncomingMail;
use PhpImap\Mailbox;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CriticalmapsCollectPositionsCommand extends ContainerAwareCommand
{
    /** @var InputInterface $input */
    protected $input;

    /** @var OutputInterface $output */
    protected $output;

    /** @var EntityManager $manager */
    protected $manager;

    /** @var string $accessToken */
    protected $accessToken;

    protected function configure()
    {
        $this
            ->setName('criticalmass:criticalmaps:collect-positions')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->manager = $this->getContainer()->get('doctrine')->getManager();

        $locations = $this->fetchLocations();

        $this->savePositions($locations);

        $this->manager->flush();
    }

    protected function fetchLocations()
    {
        $curl = new Curl();
        $curl->get('http://api.criticalmaps.net/get');

        $result = json_decode($curl->response);

        $locations = $result->locations;

        return $locations;
    }

    protected function savePositions(array $locations)
    {

    }

    protected function convertLocationToPosition()
    {
        
    }
}