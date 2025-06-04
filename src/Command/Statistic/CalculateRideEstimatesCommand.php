<?php declare(strict_types=1);

namespace App\Command\Statistic;

use App\Criticalmass\Statistic\RideEstimateHandler\RideEstimateHandlerInterface;
use App\Entity\Ride;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'criticalmass:rideestimate:recalculate',
    description: 'Recalculate ride estimates',
)]
class CalculateRideEstimatesCommand extends Command
{
    public function __construct(protected RideEstimateHandlerInterface $rideEstimateHandler, protected ManagerRegistry $registry)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $rides = $this->registry->getRepository(Ride::class)->findAll();

        $progressBar = new ProgressBar($output, count($rides));
        $table = new Table($output);
        $table->setHeaders([
            'City',
            'DateTime',
            'participations',
            'distance',
            'duration',
        ]);

        /** @var Ride $ride */
        foreach ($rides as $ride) {
            $table->addRow([
                $ride->getCity()->getCity(),
                $ride->getDateTime()->format('Y-m-d H:i'),
                $ride->getEstimatedParticipants(),
                $ride->getEstimatedDistance(),
                $ride->getEstimatedDuration(),
            ]);

            $progressBar->advance();

            $this->rideEstimateHandler
                ->setRide($ride)
                ->flushEstimates(false)
                ->calculateEstimates(false);
        }

        $this->registry->getManager()->flush();
        
        $table->render();
        $progressBar->finish();

        return Command::SUCCESS;
    }
}
