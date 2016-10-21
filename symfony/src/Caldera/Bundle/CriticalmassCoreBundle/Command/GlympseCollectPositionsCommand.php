<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\CitySlug;
use Caldera\Bundle\CalderaBundle\Entity\Ticket;
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

class GlympseCollectPositionsCommand extends ContainerAwareCommand
{
    /** @var Mailbox $mailbox */
    protected $mailbox;

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
            ->setName('criticalmass:glympse:collect-positions')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->manager = $this->getContainer()->get('doctrine')->getManager();

        $this->accessToken = $this->getAccessToken();

        $tickets = $this->getTicketsToQuery();

        /** @var Ticket $ticket */
        foreach ($tickets as $ticket) {
            $this->output->writeln(sprintf('Query ticket <info>#%d</info>', $ticket->getId()));

            $this->saveNewPositions($ticket);
        }
        
        $this->manager->flush();
    }

    protected function getAccessToken()
    {
        $hostname = $this->getContainer()->getParameter('glympse.api.hostname');
        $key = $this->getContainer()->getParameter('glympse.api.key');
        $username = $this->getContainer()->getParameter('glympse.api.username');
        $password = $this->getContainer()->getParameter('glympse.api.password');

        $loginUrl = $hostname . '/account/login';

        $curl = new Curl();
        $curl->get($loginUrl, [
            'api_key' => $key,
            'id' => $username,
            'password' => $password
        ]);

        return $curl->response->response->access_token;
    }

    protected function getTicketsToQuery()
    {
        return $this->manager->getRepository('CalderaBundle:Ticket')->findBy(
            ['queried' => false]
        );
    }

    protected function saveNewPositions(Ticket $ticket)
    {
        var_dump($this->queryTicket($ticket));
    }

    protected function queryTicket(Ticket $ticket)
    {
        $hostname = $this->getContainer()->getParameter('glympse.api.hostname');

        $invitesUrl = $hostname . '/v2/invites/' . $ticket->getInviteId();

        $curl = new Curl();
        $curl->get($invitesUrl, [
            'oauth_token' => $this->accessToken,
            'properties' => true,
            'next' => $ticket->getCounter()
        ]);

        if ($curl->response) {
            var_dump($curl->response);

            return $curl->response->response->properties;
        }

        return null;
    }
}