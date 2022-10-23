<?php declare(strict_types=1);

namespace App\Criticalmass\Image\PhotoFilterer;

use App\Entity\Photo;
use App\Entity\Ride;
use Liip\ImagineBundle\Controller\ImagineController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class PhotoFilterer
{
    /** @var ManagerRegistry $doctrine */
    protected $doctrine = null;

    /** @var UploaderHelper $uploaderHelper */
    protected $uploaderHelper = null;

    /** @var ImagineController $imagineController */
    protected $imagineController = null;

    /** @var Ride $ride */
    protected $ride;

    /** @var OutputInterface $output */
    protected $output;

    public function __construct(
        ManagerRegistry $doctrine,
        UploaderHelper $uploaderHelper,
        ImagineController $imagineController
    ) {
        $this->doctrine = $doctrine;
        $this->uploaderHelper = $uploaderHelper;
        $this->imagineController = $imagineController;
    }

    public function setRide(Ride $ride): PhotoFilterer
    {
        $this->ride = $ride;

        return $this;
    }

    public function setOutput(OutputInterface $output): PhotoFilterer
    {
        $this->output = $output;

        return $this;
    }

    public function filter(): PhotoFilterer
    {
        $photoList = $this->getPhotoList();
        $filterList = $this->getFilterList();

        $progress = new ProgressBar($this->output, count($filterList) * count($photoList));

        /** @var Photo $photo */
        foreach ($photoList as $photo) {
            foreach ($filterList as $filter) {
                $this->applyFilter($photo, $filter);

                if ($this->output->isDebug()) {
                    $this->output->writeln(sprintf(
                        'Applied filter <comment>%s</comment> to photo <info>#%d</info>',
                        $filter,
                        $photo->getId()
                    ));
                }

                $progress->advance();
            }
        }

        $progress->finish();

        return $this;
    }

    protected function applyFilter(Photo $photo, string $filter): PhotoFilterer
    {
        $filename = $this->uploaderHelper->asset($photo, 'imageFile');

        $this->imagineController
            ->filterAction(
                new Request(),
                $filename,
                $filter
            );

        return $this;
    }

    protected function getFilterList(): array
    {
        return [
            'gallery_photo_thumb',
            'gallery_photo_standard',
            'gallery_photo_large',
            'gallery_photo_blurred',
        ];
    }

    protected function getPhotoList(): array
    {
        return $this->doctrine->getRepository(Photo::class)->findPhotosByRide($this->ride);
    }
}
