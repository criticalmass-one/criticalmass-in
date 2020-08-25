<?php declare(strict_types=1);

namespace App\Command\Cycles;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Criticalmass\Cycles\Analyzer\ComparisonResultInterface;
use App\Criticalmass\Cycles\Analyzer\CycleAnalyzerInterface;
use App\Criticalmass\Cycles\Analyzer\CycleAnalyzerModel;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AnalyzeCyclesCommand extends Command
{
    /** @var CycleAnalyzerInterface $cycleAnalyzer */
    protected $cycleAnalyzer;

    /** @var ManagerRegistry $registry */
    protected $registry;

    public function __construct($name = null, CycleAnalyzerInterface $cycleAnalyzer, ManagerRegistry $registry)
    {
        $this->cycleAnalyzer = $cycleAnalyzer;
        $this->registry = $registry;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:cycles:analyze')
            ->setDescription('Compare city cycles to existing rides')
            ->addArgument('citySlug', InputArgument::REQUIRED, 'City to analyze')
            ->addOption('from', null, InputOption::VALUE_OPTIONAL, 'DateTime of period to start')
            ->addOption('until', null, InputOption::VALUE_OPTIONAL, 'DateTime of period to end');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $timezone = new \DateTimeZone('UTC');

        $city = $this->getCityBySlug($input->getArgument('citySlug'));

        if ($input->getOption('from') && $input->getOption('until')) {
            $this->cycleAnalyzer
                ->setStartDateTime(new \DateTime($input->getOption('from')))
                ->setEndDateTime(new \DateTime($input->getOption('until')));
        }

        $this->cycleAnalyzer
            ->setCity($city)
            ->analyze();

        $table = new Table($output);

        $table->setHeaders(['Ride Id', 'Cycle Id', 'Computed DateTime', 'Actual DateTime', 'Computed Location', 'Actual Location']);

        /** @var CycleAnalyzerModel $result */
        foreach ($this->cycleAnalyzer->getResultList() as $result) {
            $table->addRow([
                $result->getRide()->getId(),
                $result->getCycle() ? $result->getCycle()->getId() : '',
                $result->getGeneratedRide() ? $result->getGeneratedRide()->getDateTime()->setTimezone($timezone)->format('d.m.Y H:i') : '',
                $result->getRide()->getDateTime()->setTimezone($timezone)->format('d.m.Y H:i'),
                $result->getGeneratedRide() ? $result->getGeneratedRide()->getLocation() : '',
                $result->getRide()->getLocation(),
                $this->compare($result),
            ]);
        }

        $table->render();
    }

    protected function getCityBySlug(string $slug): City
    {
        /** @var CitySlug $citySlug */
        $citySlug = $this->registry->getRepository(CitySlug::class)->findOneBySlug($slug);

        return $citySlug->getCity();
    }

    protected function compare(CycleAnalyzerModel $model): string
    {
        switch ($model->compare()) {
            case ComparisonResultInterface::EQUAL:
                return '️✅️';

            case ComparisonResultInterface::NO_RIDE:
                return '️⚠️';

            case ComparisonResultInterface::LOCATION_MISMATCH:
                return '️❌️';

            case ComparisonResultInterface::DATETIME_MISMATCH:
                return '️❌️';

            default: return '';
        }
    }
}
