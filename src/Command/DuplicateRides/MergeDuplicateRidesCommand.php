<?php declare(strict_types=1);

namespace App\Command\DuplicateRides;

use App\Criticalmass\RideDuplicates\DuplicateFinder\DuplicateFinderInterface;
use App\Criticalmass\RideDuplicates\RideMerger\RideMergerInterface;
use App\Entity\City;
use App\Entity\Ride;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class MergeDuplicateRidesCommand extends ListDuplicateRidesCommand
{
    protected static $defaultName = 'criticalmass:ride-duplicates:merge';
    public function __construct(protected ManagerRegistry $registry, protected DuplicateFinderInterface $duplicateFinder, protected RideMergerInterface $rideMerger)
    {
         parent::__construct($registry, $duplicateFinder);
    }

    protected function configure(): void
    {
        $this->setDescription('Merge duplicate rides')
            ->addArgument(
                'citySlug',
                InputArgument::OPTIONAL,
                'City slug'
            );
    }

    protected function handleDuplicates(InputInterface $input, OutputInterface $output, array $duplicateRides): void
    {
        $firstKey = key($duplicateRides);

        /** @var City $city */
        $city = $duplicateRides[$firstKey]->getCity();

        /** @var \DateTime $dateTime */
        $dateTime = $duplicateRides[$firstKey]->getDateTime();

        $output->writeln(sprintf('Duplicates found for <info>%s</info> in <comment>%s</comment>', $city->getCity(), $dateTime->format('Y-m-d')));

        $table = new Table($output);
        $this->printTableHeader($table);

        /** @var Ride $duplicateRide */
        foreach ($duplicateRides as $duplicateRide) {
            $this->printTableRow($table, $duplicateRide);
        }

        $table->render();

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');
        $question = new Question('Enter ride id to merge everything into or press enter to skip');
        $question->setAutocompleterValues(array_keys($duplicateRides));
        $targetId = $questionHelper->ask($input, $output, $question);

        if (!$targetId) {
            return;
        }

        if (!in_array($targetId, array_keys($duplicateRides))) {
            $output->writeln(sprintf('Ride id <comment>%d</comment> is invalid!', $targetId));

            return;
        }

        $question = new ConfirmationQuestion(sprintf('Merge all <info>%d</info> rides in ride id <comment>%d</comment>?', count($duplicateRides), $targetId));

        if ($questionHelper->ask($input, $output, $question)) {
            $this->rideMerger->setTargetRide($duplicateRides[$targetId]);

            unset($duplicateRides[$targetId]);

            $this->rideMerger->addSourceRides($duplicateRides);

            $ride = $this->rideMerger->merge();

            $question = new ConfirmationQuestion('Delete old rides?');
            if ($questionHelper->ask($input, $output, $question)) {
                foreach ($duplicateRides as $sourceRide) {
                    $this->registry->getManager()->remove($sourceRide);
                }
            }

            $this->registry->getManager()->flush();
        }
    }
}
