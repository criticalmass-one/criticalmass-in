<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CriticalmassCoreBundle\Gps\LatLngListGenerator\RangeLatLngListGenerator;
use Caldera\Bundle\CriticalmassCoreBundle\Image\ExifReader\DateTimeExifReader;
use Caldera\Bundle\CriticalmassCoreBundle\Image\PhotoGps\PhotoGps;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Photo;
use Caldera\Bundle\CriticalmassModelBundle\Entity\PhotoView;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Thread;
use Caldera\Bundle\CriticalmassModelBundle\Entity\ThreadView;
use Caldera\Bundle\CriticalmassModelBundle\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StoreViewCommand extends ContainerAwareCommand
{
    /**
     * @var Registry $doctrine
     */
    protected $doctrine;

    /**
     * @var EntityManager $manager
     */
    protected $manager;

    protected $memcache;

    protected function configure()
    {
        $this
            ->setName('criticalmass:photo:storeviews')
            ->setDescription('Store saved views')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->manager = $this->doctrine->getManager();
        $this->memcache = $this->getContainer()->get('memcache.criticalmass');
    }
    
    protected function persistPhotoViews()
    {
        $photos = $this->doctrine->getRepository('CalderaCriticalmassModelBundle:Photo')->findAll();

        /**
         * @var Photo $photo
         */
        foreach ($photos as $photo) {
            $additionalPhotoViews = $this->memcache->get('gallery_photo'.$photo->getId().'_additionalviews');

            if ($additionalPhotoViews) {
                $output->writeln('Photo #'.$photo->getId().': '.$additionalPhotoViews.' views');

                for ($i = 1; $i <= $additionalPhotoViews; ++$i) {
                    $photoViewArray = $this->memcache->get('gallery_photo'.$photo->getId().'_view'.$i);

                    $user = null;

                    if ($photoViewArray['userId']) {
                        $user = $this->doctrine->getRepository('CalderaCriticalmassModelBundle:User')->find($photoViewArray['userId']);
                    }

                    $viewDateTime = new \DateTime($photoViewArray['dateTime']);

                    $photoView = new PhotoView();
                    $photoView->setPhoto($photo);
                    $photoView->setUser($user);
                    $photoView->setDateTime($viewDateTime);

                    $this->manager->persist($photoView);

                    $this->memcache->delete('gallery_photo'.$photo->getId().'_view'.$i);
                }

                $photo->setViews($photo->getViews() + $additionalPhotoViews);

                $this->manager->merge($photo);

                $this->memcache->delete('gallery_photo'.$photo->getId().'_additionalviews');

                $this->manager->flush();
            }
        }
    }

    protected function persistThreadViews()
    {
        $threads = $this->doctrine->getRepository('CalderaCriticalmassModelBundle:Thread')->findAll();

        /**
         * @var Thread $thread
         */
        foreach ($threads as $thread) {
            $additionalThreadViews = $this->memcache->get('board_thread'.$thread->getId().'_additionalviews');

            if ($additionalThreadViews) {
                $output->writeln('Thread #'.$thread->getId().': '.$additionalThreadViews.' views');

                for ($i = 1; $i <= $additionalThreadViews; ++$i) {
                    $threadViewArray = $this->memcache->get('board_thread'.$thread->getId().'_view'.$i);

                    $user = null;

                    if ($threadViewArray['userId']) {
                        $user = $this->doctrine->getRepository('CalderaCriticalmassModelBundle:User')->find($threadViewArray['userId']);
                    }

                    $viewDateTime = new \DateTime($threadViewArray['dateTime']);

                    $threadView = new ThreadView();
                    $threadView->setThread($thread);
                    $threadView->setUser($user);
                    $threadView->setDateTime($viewDateTime);

                    $this->manager->persist($threadView);

                    $this->memcache->delete('board_thread'.$thread->getId().'_view'.$i);
                }

                $thread->setViewNumber($thread->getViewNumber() + $additionalThreadViews);

                $this->manager->merge($thread);

                $this->memcache->delete('board_thread'.$thread->getId().'_additionalviews');

                $this->manager->flush();
            }
        }
    }
}
