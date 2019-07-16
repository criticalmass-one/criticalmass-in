<?php declare(strict_types=1);

namespace App\Command\Wikidata;

use App\Criticalmass\Wikidata\WikidataCityEntityFinder\WikidataCityEntityFinder;
use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Region;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Wikidata\SearchResult;

class LoadRegionsCommand extends Command
{
    /** @var WikidataCityEntityFinder $wikidataCityFinder */
    protected $wikidataCityFinder;

    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct($name = null, WikidataCityEntityFinder $wikidataCityEntityFinder, RegistryInterface $registry)
    {
        $this->wikidataCityFinder = $wikidataCityEntityFinder;
        $this->registry = $registry;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setName('criticalmass:wikidata:load-regions')
            ->addArgument('regionSlug', InputArgument::REQUIRED, 'Specify a region slug');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $questionHelper = $this->getHelper('question');

        $region = $this->registry->getRepository(Region::class)->findOneBySlug($input->getArgument('regionSlug'));

        /** @var Region $subRegion */
        foreach ($region->get as $city) {
            $output->writeln(sprintf('Current city: <info>%s</info>', $city->getCity()));

            $results = $this->wikidataCityFinder->queryForIds($city->getCity(), 'de', $proposalNumber);

            $table = new Table($output);
            $table->setHeaders([
                'Entity Id',
                'Label',
                'Description',
            ]);

            $entityIdList = [];

            /** @var SearchResult $searchResult */
            foreach ($results as $searchResult) {
                $table->addRow([
                    $searchResult->id,
                    $searchResult->label,
                    $searchResult->description,
                ]);

                $entityIdList[] = $searchResult->id;
            }

            $table->render();

            $question = new Question(sprintf('Please submit WikiData entity id or press enter to proceed:'));
            $question->setAutocompleterValues($entityIdList);

            $entityId = $questionHelper->ask($input, $output, $question);

            if ($entityId) {
                $city
                    ->setWikidataEntityId($entityId)
                    ->setUpdatedAt(new \DateTime());
            } else {
                $question = new ConfirmationQuestion('Proceed with next cities?');

                if (!$questionHelper->ask($input, $output, $question)) {
                    break;
                }
            }
        }

        $this->registry->getManager()->flush();
    }
}
