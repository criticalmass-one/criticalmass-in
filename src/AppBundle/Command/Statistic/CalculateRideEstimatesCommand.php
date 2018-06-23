<?php declare(strict_types=1);

namespace AppBundle\Command\Statistic;

use AppBundle\Entity\Ride;
use AppBundle\Criticalmass\Statistic\RideEstimate\RideEstimateService;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CalculateRideEstimatesCommand extends Command
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var RideEstimateService $rideEstimateService */
    protected $rideEstimateService;

    public function __construct(?string $name = null, RideEstimateService $rideEstimateService, RegistryInterface $registry)
    {
        $this->registry = $registry;
        $this->rideEstimateService = $rideEstimateService;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:rideestimate:recalculate')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $rides = $this->registry->getRepository(Ride::class)->findEstimatedRides();

        /** @var Ride $ride */
        foreach ($rides as $ride) {
            $output->writeln(sprintf('%s: %s', $ride->getCity()->getCity(), $ride->getFormattedDate()));

            $this->rideEstimateService->flushEstimates($ride)->calculateEstimates($ride);
        }
    }
}
