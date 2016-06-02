<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\Event;
use Caldera\Bundle\CalderaBundle\Entity\Photo;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\Thread;
use Caldera\Bundle\CalderaBundle\EntityInterface\ViewableInterface;
use Caldera\Bundle\CalderaBundle\EntityInterface\ViewInterface;
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
            ->setName('criticalmass:storeviews')
            ->setDescription('Store saved views')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->doctrine = $this->getContainer()->get('doctrine');
        $this->manager = $this->doctrine->getManager();
        $this->memcache = $this->getContainer()->get('memcache.criticalmass');

        $this->persistPhotoViews($output);
        $this->persistThreadViews($output);
        $this->persistCityViews($output);
        $this->persistEventViews($output);
        $this->persistRideViews($output);
    }

    protected function setViewEntity(ViewInterface $view, ViewableInterface $entity)
    {
        if ($entity instanceof Photo) {
            $view->setPhoto($entity);
        }

        if ($entity instanceof Thread) {
            $view->setThread($entity);
        }

        if ($entity instanceof Ride) {
            $view->setRide($entity);
        }

        if ($entity instanceof City) {
            $view->setCity($entity);
        }

        if ($entity instanceof Event) {
            $view->setEvent($entity);
        }
    }

    protected function storeViews(OutputInterface $output, $identifier, array $entities, $storageClassName)
    {
        /**
         * @var ViewableInterface $entity
         */
        foreach ($entities as $entity) {
            $additionalViews = $this->memcache->get($identifier.$entity->getId().'_additionalviews');

            if ($additionalViews) {
                $output->writeln('Entity #'.$entity->getId().': '.$additionalViews.' views');

                for ($i = 1; $i <= $additionalViews; ++$i) {
                    $viewArray = $this->memcache->get($identifier.$entity->getId().'_view'.$i);

                    $user = null;

                    if ($viewArray['userId']) {
                        $user = $this->doctrine->getRepository('CalderaBundle:User')->find($viewArray['userId']);
                    }

                    $viewDateTime = new \DateTime($viewArray['dateTime']);

                    /**
                     * @var ViewInterface $view
                     */
                    $view = new $storageClassName();

                    $this->setViewEntity($view, $entity);

                    $view->setUser($user);
                    $view->setDateTime($viewDateTime);

                    $this->manager->persist($view);

                    $this->memcache->delete($identifier.$entity->getId().'_view'.$i);
                }

                $entity->setViews($entity->getViews() + $additionalViews);

                $this->manager->merge($entity);

                $this->memcache->delete($identifier.$entity->getId().'_additionalviews');

                $this->manager->flush();
            }
        }
    }
    protected function persistPhotoViews(OutputInterface $output)
    {
        $output->writeln('Storing photo views');

        $photos = $this->doctrine->getRepository('CalderaBundle:Photo')->findAll();

        $this->storeViews($output, 'photo', $photos, 'Caldera\Bundle\CalderaBundle\Entity\PhotoView');
    }

    protected function persistThreadViews(OutputInterface $output)
    {
        $output->writeln('Storing thread views');

        $threads = $this->doctrine->getRepository('CalderaBundle:Thread')->findAll();

        $this->storeViews($output, 'thread', $threads, 'Caldera\Bundle\CalderaBundle\Entity\ThreadView');
    }

    protected function persistEventViews(OutputInterface $output)
    {
        $output->writeln('Storing event views');

        $threads = $this->doctrine->getRepository('CalderaBundle:Event')->findAll();

        $this->storeViews($output, 'event', $threads, 'Caldera\Bundle\CalderaBundle\Entity\EventView');
    }

    protected function persistRideViews(OutputInterface $output)
    {
        $output->writeln('Storing ride views');

        $rides = $this->doctrine->getRepository('CalderaBundle:Ride')->findAll();

        $this->storeViews($output, 'ride', $rides, 'Caldera\Bundle\CalderaBundle\Entity\RideView');
    }

    protected function persistCityViews(OutputInterface $output)
    {
        $output->writeln('Storing city views');

        $cities = $this->doctrine->getRepository('CalderaBundle:City')->findAll();

        $this->storeViews($output, 'city', $cities, 'Caldera\Bundle\CalderaBundle\Entity\CityView');
    }
}
