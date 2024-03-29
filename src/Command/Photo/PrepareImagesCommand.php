<?php declare(strict_types=1);

namespace App\Command\Photo;

use App\Entity\Ride;
use App\Criticalmass\Image\PhotoFilterer\PhotoFilterer;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'criticalmass:photos:prepare',
    description: 'Create thumbnails for photos',
)]
class PrepareImagesCommand extends Command
{
    public function __construct(protected ManagerRegistry $doctrine, protected PhotoFilterer $photoFilterer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $citySlug = $input->getArgument('citySlug');
        $rideIdentifier = $input->getArgument('rideIdentifier');

        $ride = $this->getRide($citySlug, $rideIdentifier);

        if (!$ride) {
            return Command::FAILURE;
        }

        $this
            ->photoFilterer
            ->setOutput($output)
            ->setRide($ride)
            ->filter()
        ;

        return Command::SUCCESS;
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
