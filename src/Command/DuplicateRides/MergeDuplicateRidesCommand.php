<?php declare(strict_types=1);

namespace App\Command\DuplicateRides;

use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Ride;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;

class MergeDuplicateRidesCommand extends ListDuplicateRidesCommand
{
    protected function configure(): void
    {
        $this
            ->setName('criticalmass:ride-duplicates:merge')
            ->setDescription('Merge duplicate rides')
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
            echo "FOO";
        }
    }
}
