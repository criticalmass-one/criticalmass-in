<?php declare(strict_types=1);

namespace App\Command\Photo;

use App\Entity\Ride;
use App\Criticalmass\Image\PhotoFilterer\PhotoFilterer;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PrepareImagesCommand extends Command
{
    public function __construct(protected ManagerRegistry $doctrine, protected PhotoFilterer $photoFilterer)
    {
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
                'rideIdentifier',
                InputArgument::REQUIRED,
                'Date of the ride'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $citySlug = $input->getArgument('citySlug');
        $rideIdentifier = $input->getArgument('rideIdentifier');

        $ride = $this->getRide($citySlug, $rideIdentifier);

        if (!$ride) {
            return;
        }

        $this->photoFilterer
            ->setOutput($output)
            ->setRide($ride)
            ->filter();
    }

    protected function getRide(string $citySlug, string $rideIdentifier): ?Ride
    {
        $ride = $this->doctrine->getRepository(Ride::class)->findByCitySlugAndRideDate($citySlug, $rideIdentifier);

        if (!$ride) {
            $ride = $this->doctrine->getRepository(Ride::class)->findOneByCitySlugAndSlug($citySlug, $rideIdentifier);
        }

        return $ride;
    }
}
