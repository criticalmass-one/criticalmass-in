<?php

namespace AppBundle\Command\Photo;

use AppBundle\Entity\Ride;
use AppBundle\Criticalmass\Image\PhotoFilterer\PhotoFilterer;
use Doctrine\Bundle\DoctrineBundle\Registry as Doctrine;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PrepareImagesCommand extends Command
{
    /** @var Doctrine $doctrine */
    protected $doctrine;

    /** @var PhotoFilterer $photoFilterer */
    protected $photoFilterer;

    public function __construct(Doctrine $doctrine, PhotoFilterer $photoFilterer)
    {
        $this->doctrine = $doctrine;
        $this->photoFilterer = $photoFilterer;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:photos:prepare')
            ->setDescription('Create thumbnails for photos')
            ->addArgument(
                'citySlug',
                InputArgument::REQUIRED,
                'Slug of the city'
            )
            ->addArgument(
                'rideDate',
                InputArgument::REQUIRED,
                'Date of the ride'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $citySlug = $input->getArgument('citySlug');
        $rideDate = $input->getArgument('rideDate');

        $ride = $this->getRide($citySlug, $rideDate);

        if (!$ride) {
            return;
        }

        $this->photoFilterer
            ->setOutput($output)
            ->setRide($ride)
            ->filter();
    }

    protected function getRide(string $citySlug, string $rideDate): ?Ride
    {
        return $this->doctrine->getRepository(Ride::class)->findByCitySlugAndRideDate($citySlug, $rideDate);
    }
}
