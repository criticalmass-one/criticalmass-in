<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Command\Cycles;

use Criticalmass\Bundle\AppBundle\Entity\City;
use Criticalmass\Bundle\AppBundle\Entity\CitySlug;
use Criticalmass\Component\Cycles\Analyzer\CycleAnalyzerInterface;
use Criticalmass\Component\Cycles\Analyzer\CycleAnalyzerModel;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AnalyzeCyclesCommand extends Command
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
            ->setName('criticalmass:cycles:analyze')
            ->setDescription('Compare city cycles to existing rides')
            ->addArgument(
                'citySlug',
                InputArgument::REQUIRED,
                'City to analyze'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $city = $this->getCityBySlug($input->getArgument('citySlug'));

        $this->cycleAnalyzer->setCity($city)->analyze();

        $table = new Table($output);

        $table->setHeaders(['Cycle Id', 'Computed DateTime', 'Actual DateTime', 'Computed Location', 'Actual Location']);

        /** @var CycleAnalyzerModel $result */
        foreach ($this->cycleAnalyzer->getResultList() as $result) {
            $table->addRow([
                $result->getCycle() ? $result->getCycle()->getId() : '',
                $result->getGeneratedRide() ? $result->getGeneratedRide()->getDateTime()->format('d.m.Y H:i') : '',
                $result->getRide()->getDateTime()->format('d.m.Y H:i'),
                $result->getGeneratedRide() ? $result->getGeneratedRide()->getLocation() : '',
                $result->getRide()->getLocation(),
                $result->equals() ? '✅' : '❌',
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
}
