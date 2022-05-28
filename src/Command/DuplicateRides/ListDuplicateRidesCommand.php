<?php declare(strict_types=1);

namespace App\Command\DuplicateRides;

use App\Criticalmass\RideDuplicates\DuplicateFinder\DuplicateFinderInterface;
use App\Entity\City;
use App\Entity\CitySlug;
use App\Entity\Ride;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListDuplicateRidesCommand extends Command
{
    protected ManagerRegistry $registry;
    protected DuplicateFinderInterface $duplicateFinder;

    public function __construct(ManagerRegistry $registry, DuplicateFinderInterface $duplicateFinder)
    {
        $this->registry = $registry;
        $this->duplicateFinder = $duplicateFinder;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:ride-duplicates:list')
            ->setDescription('Find duplicate rides')
            ->addArgument(
                'citySlug',
                InputArgument::OPTIONAL,
                'City slug'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var string $citySlug */
        $citySlugString = $input->getArgument('citySlug');

        if ($citySlugString) {
            /** @var CitySlug $citySlug */
            $citySlug = $this->registry->getRepository(CitySlug::class)->findOneBySlug($citySlugString);

            if (!$citySlug) {
                $output->writeln(sprintf('No city found with slug "%"', $citySlugString));
            }

            $city = $citySlug->getCity();

            $this->duplicateFinder->setCity($city);
        }

        $duplicateRideList = $this->duplicateFinder->findDuplicates();

        foreach ($duplicateRideList as $duplicateRides) {
            $this->handleDuplicates($input, $output, $duplicateRides);
        }
    }

    protected function handleDuplicates(InputInterface $input, OutputInterface $output, array $duplicateRides): void
    {
        $firstKey = array_key_first($duplicateRides);

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
    }

    protected function printTableHeader(Table $table): void
    {
        $table->setHeaders([
            'ID',
            'slug',
            'dateTime',
            'Location',
            'Description',
            'Participations',
            'Views',
            'Estimations',
            'Tracks',
            'Comments',
            'LastUpdate',
        ]);
    }

    protected function printTableRow(Table $table, Ride $ride): void
    {
        $table->addRow([
            $ride->getId(),
            $ride->getSlug(),
            $ride->getDateTime()->format('Y-m-d H:i:s'),
            sprintf('%s (%f, %f)', $ride->getLocation(), $ride->getLatitude(), $ride->getLongitude()),
            substr($ride->getDescription() ?? '', 0, 32),
            $ride->getParticipations()->count(),
            $ride->getViews(),
            $ride->getEstimates()->count(),
            $ride->getTracks()->count(),
            $ride->getPosts()->count(),
            $ride->getUpdatedAt() ? $ride->getUpdatedAt()->format('Y-m-d H:i:s') : '',
        ]);
    }
}
