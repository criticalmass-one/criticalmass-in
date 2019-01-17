<?php declare(strict_types=1);

namespace App\Command\DuplicateRides;

use App\Criticalmass\RideDuplicates\DuplicateFinder\DuplicateFinderInterface;
use App\Entity\CitySlug;
use App\Entity\Ride;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\TranslatorInterface;

class ListDuplicateRidesCommand extends Command
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var DuplicateFinderInterface $duplicateFinder */
    protected $duplicateFinder;

    public function __construct($name = null, RegistryInterface $registry, DuplicateFinderInterface $duplicateFinder)
    {
        $this->registry = $registry;
        $this->duplicateFinder = $duplicateFinder;

        parent::__construct($name);
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

        $table = new Table($output);

        foreach ($duplicateRideList as $duplicateRides) {
            /** @var Ride $rideA */
            $rideA = array_pop($duplicateRides);
            /** @var Ride $rideB */
            $rideB = array_pop($duplicateRides);

            $table->addRow([
                $rideA->getCity()->getCity(),
                $rideA->getDateTime()->format('Y-m-d'),
                $rideA->getId(),
                $rideB->getId(),
                $rideA->getLocation(),
                $rideB->getLocation(),
            ]);
        }

        $table->render();
    }
}
