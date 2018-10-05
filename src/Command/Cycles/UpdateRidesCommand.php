<?php declare(strict_types=1);

namespace App\Command\Cycles;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Criticalmass\Cycles\Analyzer\ComparisonResultInterface;
use App\Criticalmass\Cycles\Analyzer\CycleAnalyzerInterface;
use App\Criticalmass\Cycles\Analyzer\CycleAnalyzerModel;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class UpdateRidesCommand extends Command
{
    /** @var CycleAnalyzerInterface $cycleAnalyzer */
    protected $cycleAnalyzer;

    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct($name = null, CycleAnalyzerInterface $cycleAnalyzer, RegistryInterface $registry)
    {
        $this->cycleAnalyzer = $cycleAnalyzer;
        $this->registry = $registry;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:cycles:update')
            ->setDescription('Compare city cycles to existing rides')
            ->addArgument('citySlug', InputArgument::REQUIRED, 'City to analyze');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $timezone = new \DateTimeZone('UTC');

        $city = $this->getCityBySlug($input->getArgument('citySlug'));

        $this->cycleAnalyzer->setCity($city)->analyze();

        $questionHelper = $this->getHelper('question');

        $propertyList = ['skip', 'all', 'date', 'time', 'dateTime', 'location', 'latitude', 'longitude', 'coord'];

        /** @var CycleAnalyzerModel $result */
        foreach ($this->cycleAnalyzer->getResultList() as $result) {
            if (!$result->getCycle()) {
                $output->writeln(sprintf('Ride <info>%d</info> (<comment>%s</comment>) has no assigned cycle, skipping', $result->getRide()->getId(), $result->getRide()->getDateTime()->format('Y-m-d H:i')));

                continue;
            }

            $ride = $result->getRide();
            $generatedRide = $result->getGeneratedRide();

            $table = new Table($output);

            $table->setHeaders(['Ride Id', 'Cycle Id', 'Computed DateTime', 'Actual DateTime', 'Computed Location', 'Actual Location']);

            $table->addRow([
                $ride->getId(),
                $result->getCycle()->getId(),
                $generatedRide->getDateTime()->setTimezone($timezone)->format('d.m.Y H:i'),
                $ride->getDateTime()->setTimezone($timezone)->format('d.m.Y H:i'),
                sprintf('%s (%f, %f)', $result->getGeneratedRide()->getLocation(), $result->getGeneratedRide()->getLatitude(), $result->getGeneratedRide()->getLongitude()),
                sprintf('%s (%f, %f)', $result->getRide()->getLocation(), $result->getRide()->getLatitude(), $result->getRide()->getLongitude()),
                $this->compare($result),
            ]);

            $table->render();

            $question = new ChoiceQuestion('Please select which properties to transfer from generated to actual ride', $propertyList);
            $question->setMultiselect(true);

            $selectedProperties = $questionHelper->ask($input, $output, $question);

            if (in_array('skip', $selectedProperties)) {
                continue;
            }

            if (in_array('all', $selectedProperties)) {
                $selectedProperties = array_slice($propertyList, 2, count($propertyList) - 2);
            }

            foreach ($selectedProperties as $property) {
                $setMethodName = sprintf('set%s', ucfirst($property));
                $getMethodName = sprintf('get%s', ucfirst($property));

                $ride->$setMethodName($generatedRide->$getMethodName());
            }

            $output->writeln(sprintf('Updated following properties at ride entity <info>%d</info>: <comment>%s</comment>', $ride->getId(), implode(', ', $selectedProperties)));
        }
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
