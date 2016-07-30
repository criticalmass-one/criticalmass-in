<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CalderaBundle\Repository\RideRepository;
use Caldera\Bundle\CriticalmassCoreBundle\Notification\Dispatcher\NotificationDispatcher;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class NotifyCommand extends ContainerAwareCommand
{
    /** @var Registry $doctrine */
    protected $doctrine;

    /** @var EntityManager $manager */
    protected $manager;
    
    /** @var NotificationDispatcher $notificationDispatcher */
    protected $notificationDispatcher;
    
    protected function configure()
    {
        $this
            ->setName('criticalmass:notification:send')
            ->setDescription('Send notifications')
            ->addOption('city-slug', 'cs', InputOption::VALUE_REQUIRED)
            ->addOption('ride-date', 'rd', InputOption::VALUE_REQUIRED)
            ->addOption('type', 't', InputOption::VALUE_REQUIRED)
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->manager = $this->doctrine->getManager();
        $this->notificationDispatcher = $this->getContainer()->get('caldera.criticalmass.notification.dispatcher');

        $this->input = $input;
        $this->output = $output;

        if ($this->input->getOption('type') == 'rideLocationPublished' and
            $this->input->getOption('city-slug') and
            $this->input->getOption('ride-date'))
        {
            $this->notifiyRideLocation();
        }
    }

    protected function notifiyRideLocationPublished()
    {
        $citySlug = $this->input->getOption('city-slug');
        $rideDate = $this->input->getOption('ride-date');

        /** @var RideRepository $rideRepository */
        $rideRepository = $this->manager->getRepository('CalderaBundle:Ride');
        $ride = $rideRepository->findByCitySlugAndRideDate($citySlug, $rideDate);
/*
        $this->notificationDispatcher
            ->setNotification($notificaition);*/
        $this->output->writeln($ride->getId());
    }
}