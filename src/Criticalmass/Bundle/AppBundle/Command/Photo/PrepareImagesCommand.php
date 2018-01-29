<?php

namespace Criticalmass\Bundle\AppBundle\Command\Photo;

use Criticalmass\Bundle\AppBundle\Entity\Photo;
use Criticalmass\Bundle\AppBundle\Entity\Ride;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Liip\ImagineBundle\Controller\ImagineController;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class PrepareImagesCommand extends ContainerAwareCommand
{
    /** @var Registry $doctrine */
    protected $doctrine = null;

    /** @var UploaderHelper $uploaderHelper*/
    protected $uploaderHelper = null;

    /** @var ImagineController $imagineController */
    protected $imagineController = null;

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
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->uploaderHelper = $this->getContainer()->get('vich_uploader.templating.helper.uploader_helper');
        $this->imagineController = $this->getContainer()->get('liip_imagine.controller');

        /** @var Ride $ride */
        $ride = $this->doctrine->getRepository('AppBundle:Ride')->findByCitySlugAndRideDate($input->getArgument('citySlug'), $input->getArgument('rideDate'));

        $photoList = $this->doctrine->getRepository('AppBundle:Photo')->findPhotosByRide($ride);

        $filterList = $this->getFilterList();

        $progress = new ProgressBar($output, count($filterList) * count($photoList));

        /** @var Photo $photo */
        foreach ($photoList as $photo) {
            foreach ($filterList as $filter) {
                $this->applyFilter($photo, $filter);

                if ($output->isDebug()) {
                    $output->writeln(sprintf(
                        'Applied filter <comment>%s</comment> to photo <info>#%d</info>',
                        $filter,
                        $photo->getId()
                    ));
                }

                $progress->advance();
            }
        }

        $progress->finish();
    }

    protected function applyFilter(Photo $photo, string $filter): void
    {
        $filename = $this->uploaderHelper->asset($photo, 'imageFile');

        $this->imagineController
            ->filterAction(
                new Request(),
                $filename,
                $filter
            )
        ;
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
}
