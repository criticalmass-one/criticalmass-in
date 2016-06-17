<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyCommand extends ContainerAwareCommand
{
    /**
     * @var Registry $doctrine
     */
    protected $doctrine;

    /**
     * @var EntityManager $manager
     */
    protected $manager;

    protected $messageBird;

    protected function configure()
    {
        $this
            ->setName('criticalmass:notification:send')
            ->setDescription('Send notifications')
            ->addOption('ride-id', InputOption::VALUE_REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->manager = $this->doctrine->getManager();
        $this->notificationDispatcher = $this->getContainer()->get('caldera.criticalmass.notification.dispatcher');

        if ($input->getOption('ride-id')) {
            $ride = $this->manager->getRepository('Caldera:Ride')->find($input->getOption('ride-id'));

            $output->writeln($ride->getId());
        }
    }
}