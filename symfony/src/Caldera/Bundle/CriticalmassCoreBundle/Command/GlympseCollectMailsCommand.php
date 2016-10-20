<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CriticalmassCoreBundle\Statistic\RideEstimate\RideEstimateService;
use PhpImap\Mailbox;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GlympseCollectMailsCommand extends ContainerAwareCommand
{
    /** @var Mailbox $mailbox */
    protected $mailbox;

    protected function configure()
    {
        $this
            ->setName('criticalmass:glympse:collect-mails')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->connectMailbox();
    }

    protected function connectMailbox()
    {
        $host = $this->getContainer()->getParameter('glympse.imap.hostname');
        $port = $this->getContainer()->getParameter('glympse.imap.port');
        $username = $this->getContainer()->getParameter('glympse.imap.username');
        $password = $this->getContainer()->getParameter('glympse.imap.password');

        $this->mailbox = new Mailbox('{'.$host.':'.$port.'/novalidate-cert/imap/ssl}INBOX', $username, $password);
    }
}