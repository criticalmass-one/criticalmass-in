<?php declare(strict_types=1);

namespace App\Command\Photo;

use App\Entity\Photo;
use App\Entity\Ride;
use App\Entity\Track;
use App\Entity\User;
use App\Criticalmass\Image\PhotoUploader\PhotoUploader;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportImagesCommand extends Command
{
    public function __construct(protected ManagerRegistry $registry, protected PhotoUploader $photoUploader)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:photos:import')
            ->setDescription('Import photos to a tour')
            ->addArgument(
                'citySlug',
                InputArgument::REQUIRED,
                'Slug of the city'
            )
            ->addArgument(
                'rideIdentifier',
                InputArgument::REQUIRED,
                'Date or slug of the ride'
            )
            ->addArgument(
                'username',
                InputArgument::REQUIRED,
                'Name of the user'
            )
            ->addArgument(
                'path',
                InputArgument::REQUIRED,
                'Path of the image directory'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $ride = $this->getRide($input->getArgument('citySlug'), $input->getArgument('rideIdentifier'));
        $user = $this->registry->getRepository(User::class)->findOneByUsername($input->getArgument('username'));
        $track = $this->registry->getRepository(Track::class)->findByUserAndRide($ride, $user);

        $this->photoUploader
            ->setRide($ride)
            ->setUser($user)
            ->setTrack($track)
            ->addDirectory($input->getArgument('path'));

        $table = new Table($output);
        $table->setHeaders(['Filename', 'DateTime', 'Coords']);

        /** @var Photo $photo */
        foreach ($this->photoUploader->getAddedPhotoList() as $photo) {
            $table->addRow([
                $photo->getImageName(),
                $photo->getExifCreationDate() ? $photo->getExifCreationDate()->format('Y-m-d H:i:s') : '',
                $photo->hasCoordinates() ? sprintf('%f,%f', $photo->getLatitude(), $photo->getLongitude()) : ''
            ]);
        }

        $table->render();

    }

    protected function getRide(string $citySlug, string $rideIdentifier): ?Ride
    {
        $ride = $this->registry->getRepository(Ride::class)->findByCitySlugAndRideDate($citySlug, $rideIdentifier);

        if (!$ride) {
            $ride = $this->registry->getRepository(Ride::class)->findOneByCitySlugAndSlug($citySlug, $rideIdentifier);
        }

        return $ride;
    }
}
