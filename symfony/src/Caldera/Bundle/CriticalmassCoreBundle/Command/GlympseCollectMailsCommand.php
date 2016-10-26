<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\CitySlug;
use Caldera\Bundle\CalderaBundle\Entity\Ticket;
use Caldera\Bundle\CriticalmassCoreBundle\Statistic\RideEstimate\RideEstimateService;
use Doctrine\ORM\EntityManager;
use PhpImap\IncomingMail;
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

    /** @var InputInterface $input */
    protected $input;

    /** @var OutputInterface $output */
    protected $output;

    /** @var EntityManager $manager */
    protected $manager;

    protected function configure()
    {
        $this
            ->setName('criticalmass:glympse:collect-mails')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->manager = $this->getContainer()->get('doctrine')->getManager();

        $this->connectMailbox();

        $unreadMails = $this->catchUnreadMails();

        foreach ($unreadMails as $unreadMail) {
            $invitationCode = $this->grepInvitationCode($unreadMail);
            $citySlug = $this->grepCitySlug($unreadMail);

            if ($invitationCode && $citySlug) {
                $this->output->writeln(sprintf('Found invitation code <comment>%s</comment> for <info>%s</info>', $invitationCode, $citySlug));
                $this->saveInvitation($citySlug, $invitationCode);
            } else {
                $this->output->writeln(sprintf('Could not grep city slug and invitation code from mail <info>%d</info>', $unreadMail->id));
            }
        }

        $this->manager->flush();
    }

    protected function connectMailbox()
    {
        $host = $this->getContainer()->getParameter('glympse.imap.hostname');
        $port = $this->getContainer()->getParameter('glympse.imap.port');
        $username = $this->getContainer()->getParameter('glympse.imap.username');
        $password = $this->getContainer()->getParameter('glympse.imap.password');

        $this->mailbox = new Mailbox('{'.$host.':'.$port.'/novalidate-cert/imap/ssl}INBOX', $username, $password);
    }

    protected function catchUnreadMails()
    {
        $unreadMailIds = $this->mailbox->searchMailbox('TO "hamburg@criticalmass.in"');
        $unreadMails = [];

        foreach ($unreadMailIds as $unreadMailId) {
            $unreadMails[$unreadMailId] = $this->mailbox->getMail($unreadMailId);
        }

        return $unreadMails;
    }

    protected function grepInvitationCode(IncomingMail $mail)
    {
        $plainBody = $mail->textPlain;

        preg_match('/([0-9A-Z]{2,4})\-([0-9A-Z]{2,4})/', $plainBody, $matches);

        $invitationCode = null;

        if ($matches && is_array($matches) && count($matches) == 3) {
            $invitationCode = $matches[0];
        }

        return $invitationCode;
    }

    protected function grepCitySlug(IncomingMail $mail)
    {
        $toString = $mail->toString;

        preg_match('/([a-z0-9\-]{3,})\@criticalmass\.in/i', $toString, $matches);

        $citySlug = null;

        if ($matches && is_array($matches) && count($matches) == 2) {
            $citySlug = $matches[1];
        }

        return $citySlug;
    }

    protected function saveInvitation(string $citySlug, string $invitationCode)
    {
        /** @var CitySlug $citySlug */
        $citySlug = $this->manager->getRepository('CalderaBundle:CitySlug')->findOneBySlug($citySlug);

        if (!$citySlug) {
            return;
        }

        /** @var City $city */
        $city = $citySlug->getCity();

        if (!$city) {
            return;
        }

        $ticket = new Ticket();

        $ticket
            ->setCity($city)
            ->setColorBlue(rand(0, 255))
            ->setColorRed(rand(0, 255))
            ->setColorGreen(rand(0, 255))
            ->setInviteId($invitationCode);

        $this->manager->persist($ticket);
    }
}