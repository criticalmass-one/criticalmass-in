<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CriticalmassCoreBundle\StandardRideGenerator\StandardRideGenerator;
use Caldera\Bundle\CriticalmassCoreBundle\Statistic\RideEstimate\RideEstimateService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CalculateRideEstimatesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('criticalmass:rideestimate:recalculate')
            ->setDescription('')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var RideEstimateService
         */
        $res = $this->getContainer()->get('caldera.criticalmass.statistic.rideestimate');
        
        $rides = $this->getContainer()->get('doctrine')->getRepository('CalderaBundle:Ride')->findEstimatedRides();
        
        foreach ($rides as $ride) {
            $output->writeln($ride->getCity()->getCity().': '.$ride->getFormattedDate());

            $res->flushEstimates($ride);
            $res->calculateEstimates($ride);
        }
    }
}