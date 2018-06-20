<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Command\Statistic;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportRideEstimatesCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('criticalmass:rideestimate:import')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $importLines = $this->readFromStdin();

        var_dump($importLines);
    }

    protected function readFromStdin(): array
    {
        $lines = [];

        if (0 === ftell(STDIN)) {
            while (!feof(STDIN)) {
                $lines[] = fread(STDIN, 1024);
            }
        }

        return $lines;
    }
}
