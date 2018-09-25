<?php declare(strict_types=1);

namespace App\Command\Cycles;

use App\Entity\City;
use App\Entity\Ride;
use App\Criticalmass\RideGenerator\RideGenerator\RideGeneratorInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class GenerateRidesCommand extends Command
{
    /** @var RideGeneratorInterface $rideGenerator */
    protected $rideGenerator;

    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct($name = null, RideGeneratorInterface $rideGenerator, RegistryInterface $registry)
    {
        $this->rideGenerator = $rideGenerator;
        $this->registry = $registry;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:cycles:generate-rides')
            ->setDescription('Create rides for a parameterized year and month automatically')
            ->addArgument(
                'year',
                InputArgument::REQUIRED,
                'Year of the rides to create'
            )
            ->addArgument(
                'month',
                InputArgument::REQUIRED,
                'Month of the rides to create'
            )
            ->addArgument(
                'cities',
                InputArgument::IS_ARRAY,
                'List of cities'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var int $year */
        $year = (int) $input->getArgument('year');

        /** @var int $month */
        $month = (int) $input->getArgument('month');

        $manager = $this->registry->getManager();

        $cityList = $this->getCityList($input);

        $this->rideGenerator
            ->setMonth($month)
            ->setYear($year)
            ->setCityList($cityList)
            ->execute();

        $table = new Table($output);
        $table->setHeaders(['City', 'DateTime Location', 'DateTime UTC', 'Location']);

        $utc = new \DateTimeZone('UTC');

        $counter = 0;

        /** @var Ride $ride */
        foreach ($this->rideGenerator->getRideList() as $ride) {
            $table->addRow([
                $ride->getCity()->getCity(),
                $ride->getDateTime()->format('Y-m-d H:i'),
                $ride->getDateTime()->setTimezone($utc)->format('Y-m-d H:i'),
                $ride->getLocation(),
            ]);

            $manager->persist($ride);

            ++$counter;
        }

        $table->render();

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Save all created rides?', false);

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $manager->flush();

        $output->writeln(sprintf('Saved %d rides', $counter));
    }

    protected function getCityList(InputInterface $input): array
    {
        $citySlugList = $input->getArgument('cities');

        if (count($citySlugList) === 0) {
            return $this->registry->getRepository(City::class)->findCities();
        }

        return $this->registry->getRepository(City::class)->findCitiesBySlugList($citySlugList);
    }
}
