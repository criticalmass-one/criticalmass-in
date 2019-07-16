<?php declare(strict_types=1);

namespace App\Command\Wikidata;

use App\Criticalmass\Wikidata\CityPopulationFetcher\CityPopulationFetcherInterface;
use App\Criticalmass\Wikidata\WikidataCityEntityFinder\WikidataCityEntityFinder;
use App\Entity\City;
use App\Entity\CitySlug;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Wikidata\SearchResult;

class UpdatePopulationCommand extends Command
{
    /** @var CityPopulationFetcherInterface $cityPopulationFetcher */
    protected $cityPopulationFetcher;

    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct($name = null, CityPopulationFetcherInterface $cityPopulationFetcher, RegistryInterface $registry)
    {
        $this->cityPopulationFetcher = $cityPopulationFetcher;
        $this->registry = $registry;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('criticalmass:wikidata:update-city-population');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $cityList = $this->registry->getRepository(City::class)->findCitiesWithWikidataEntityId();

        $progressBar = new ProgressBar($output, count($cityList));

        $table = new Table($output);
        $table->setHeaders([
            'City',
            'Wikidata entity id',
            'New population',
            'Old population',
            'Delta',
        ]);

        /** @var City $city */
        foreach ($cityList as $city) {
            $oldPopulation = $city->getCityPopulation();

            if ($newPopulation = $this->cityPopulationFetcher->fetch($city)) {
                $city
                    ->setCityPopulation($newPopulation)
                    ->setUpdatedAt(new \DateTime())
                    ->setUser(null);
            }

            $table->addRow([
                $city->getCity(),
                $city->getWikidataEntityId(),
                $newPopulation,
                $oldPopulation,
                ($oldPopulation && $newPopulation ? $newPopulation - $oldPopulation : ''),
            ]);

            $progressBar->advance();
        }

        $progressBar->finish();

        $table->render();

        $questionHelper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Save changes?');

        if ($questionHelper->ask($input, $output, $question)) {
            $this->registry->getManager()->flush();
        }
    }
}
