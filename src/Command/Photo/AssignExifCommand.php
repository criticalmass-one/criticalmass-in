<?php declare(strict_types=1);

namespace App\Command\Photo;

use App\Criticalmass\Image\ExifHandler\ExifHandlerInterface;
use App\Entity\Photo;
use App\Entity\Track;
use App\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AssignExifCommand extends Command
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var ExifHandlerInterface $exifHandler */
    protected $exifHandler;

    public function __construct(RegistryInterface $registry, ExifHandlerInterface $exifHandler)
    {
        $this->registry = $registry;
        $this->exifHandler = $exifHandler;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('criticalmass:photos:assign-exif')
            ->setDescription('Assign exif data to photos');
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $photoList = $this->registry->getRepository(Photo::class)->findPhotosWithoutExifData();

        $progressBar = new ProgressBar($output, count($photoList));

        /** @var Photo $photo */
        foreach ($photoList as $photo) {
            $exif = $this->exifHandler->readExifDataFromPhotoFile($photo);

            if ($exif) {
                $this->exifHandler->assignExifDataToPhoto($photo, $exif);
            }

            $progressBar->advance();
        }

        $this->registry->getManager()->flush();

        $progressBar->finish();
    }
}
