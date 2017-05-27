<?php

namespace AppBundle\Command;

use AppBundle\Entity\Photo;
use AppBundle\Entity\Ride;
use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Liip\ImagineBundle\Controller\ImagineController;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class PrepareImagesCommand extends ContainerAwareCommand
{
    /** @var Registry $doctrine */
    protected $doctrine;

    protected function configure()
    {
        $this
            ->setName('criticalmass:images:prepare')
            ->setDescription('Create thumbnails for images')
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');

        /** @var Ride $ride */
        $ride = $this->doctrine->getRepository('AppBundle:Ride')->findByCitySlugAndRideDate($input->getArgument('citySlug'), $input->getArgument('rideDate'));

        $photos = $this->doctrine->getRepository('AppBundle:Photo')->findPhotosByRide($ride);

        /** @var Photo $photo */
        foreach ($photos as $photo) {
            $this->filter($photo);

            $output->writeln(sprintf(
                'Filtered photo <comment>#%d</comment>',
                $photo->getId()
            ));
        }


    }

    protected function filter(Photo $photo)
    {
        $uploaderHelper = $this->getContainer()->get('vich_uploader.templating.helper.uploader_helper');

        $filename = $uploaderHelper->asset($photo, 'imageFile');

        $request = new Request();

        /** @var ImagineController */
        $imagine = $this
            ->getContainer()
            ->get('liip_imagine.controller');

        /** @var RedirectResponse */
        $imagemanagerResponse = $imagine
            ->filterAction(
                $request,
                $filename,
                'gallery_photo_thumb'
            );

        var_dump($imagemanagerResponse);
    }
}