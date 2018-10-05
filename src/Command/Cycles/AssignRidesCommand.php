<?php declare(strict_types=1);

namespace App\Command\Cycles;

use App\Entity\City;
use App\Entity\CityCycle;
use App\Entity\CitySlug;
use App\Criticalmass\Cycles\Analyzer\ComparisonResultInterface;
use App\Criticalmass\Cycles\Analyzer\CycleAnalyzerInterface;
use App\Criticalmass\Cycles\Analyzer\CycleAnalyzerModel;
use App\Entity\Ride;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class AssignRidesCommand extends Command
{
    /** @var CycleAnalyzerInterface $cycleAnalyzer */
    protected $cycleAnalyzer;

    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var array $cycleCache */
    protected $cycleCache = [];

    public function __construct($name = null, CycleAnalyzerInterface $cycleAnalyzer, RegistryInterface $registry)
    {
        $this->cycleAnalyzer = $cycleAnalyzer;
        $this->registry = $registry;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:cycles:assign')
            ->setDescription('Assign existing rides to city cycles')
            ->addArgument('citySlug', InputArgument::REQUIRED, 'City to analyze')
            ->addOption('from', null, InputOption::VALUE_OPTIONAL, 'DateTime of period to start')
            ->addOption('until', null, InputOption::VALUE_OPTIONAL, 'DateTime of period to end');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $city = $this->getCityBySlug($input->getArgument('citySlug'));
        $fromDateTime = $input->getOption('from') ? new \DateTime($input->getOption('from')) : null;
        $untilDateTime = $input->getOption('until') ? new \DateTime($input->getOption('until')) : null;

        $rideList = $this->registry->getRepository(Ride::class)->findRides($fromDateTime, $untilDateTime, $city);

        $output->writeln(sprintf('Found <info>%d</info> rides for city <comment>%s</comment>', count($rideList), $city->getCity()));

        $questionHelper = $this->getHelper('question');
        $question = new Question('Please submit cycle id or press enter to proceed:', '');

        /** @var Ride $ride */
        foreach ($rideList as $ride) {
            if ($ride->getCycle()) {
                $output->writeln(sprintf('Ride <info>%d</info> at <comment>%s</comment> is assigned to cycle <info>%d</info>.', $ride->getId(), $ride->getDateTime()->format('Y-m-d H:i:s'), $ride->getCycle()->getId()));
            } else {
                $output->writeln(sprintf('Ride <info>%d</info> at <comment>%s</comment> is not assigned to a cycle.', $ride->getId(), $ride->getDateTime()->format('Y-m-d H:i:s')));
            }

            $newCycleId = $questionHelper->ask($input, $output, $question);

            if ($newCycleId = intval($newCycleId)) {
                $ride->setCycle($this->getCycleById($newCycleId));

                $output->writeln(sprintf('Assigned cycle <info>%d</info> to ride.', $newCycleId));
            }
        }

        $this->registry->getManager()->flush();
    }

    protected function getCityBySlug(string $slug): City
    {
        /** @var CitySlug $citySlug */
        $citySlug = $this->registry->getRepository(CitySlug::class)->findOneBySlug($slug);

        return $citySlug->getCity();
    }

    protected function getCycleById(int $cycleId): ?CityCycle
    {
        if (!array_key_exists($cycleId, $this->cycleCache)) {
            $this->cycleCache[$cycleId] = $this->registry->getRepository(CityCycle::class)->find($cycleId);
        }

        return $this->cycleCache[$cycleId];
    }
}
