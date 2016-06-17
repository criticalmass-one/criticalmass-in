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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->manager = $this->doctrine->getManager();
        $this->notificationDispatcher = $this->getContainer()->get('caldera.criticalmass.notification.dispatcher');

        if ($input->getOption('city-slug') and $input->getOption('ride-date')) {
            $citySlug = $input->getOption('city-slug');
            $rideDate = $input->getOption('ride-date');

            /** @var RideRepository $rideRepository */
            $rideRepository = $this->manager->getRepository('CalderaBundle:Ride');
            $ride = $rideRepository->findByCitySlugAndRideDate($citySlug, $rideDate);

            $output->writeln($ride->getId());
        }
    }
}