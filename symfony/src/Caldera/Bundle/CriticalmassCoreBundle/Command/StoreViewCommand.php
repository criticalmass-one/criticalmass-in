<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CalderaBundle\Entity\BlogPost;
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
    /** @var OutputInterface $output */
    protected $output;

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
        $this->output = $output;

        $this->persistPhotoViews();
        $this->persistThreadViews();
        $this->persistCityViews();
        $this->persistEventViews();
        $this->persistRideViews();
        $this->persistBlogPostViews();
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

        if ($entity instanceof BlogPost) {
            $view->setBlogPost($entity);
        }
    }

    protected function storeViews(string $entityClassName)
    {
        $identifier = lcfirst($entityClassName);
        $storageClassName = 'Caldera\Bundle\CalderaBundle\Entity\\'.$entityClassName.'View';

        $serializedViewsArray = $this->memcache->get($identifier.'_views');

        if ($serializedViewsArray) {
            $viewsArray = unserialize($serializedViewsArray);
            
            foreach ($viewsArray as $view) {
                $user = null;

                if ($view['userId']) {
                    $user = $this->doctrine->getRepository('CalderaBundle:User')->find($view['userId']);
                }

                $viewDateTime = new \DateTime($view['dateTime']);

                /**
                 * @var ViewInterface $viewEntity
                 */
                $viewEntity = new $storageClassName();

                $entity = $this->manager->getRepository('CalderaBundle:'.$entityClassName)->find($view['entityId']);

                $this->setViewEntity($viewEntity, $entity);
                $viewEntity->setUser($user);
                $viewEntity->setDateTime($viewDateTime);

                $this->manager->persist($viewEntity);

                $this->output->writeln('Storing view for '.$entityClassName.' #'.$view['entityId'].' ('.$viewDateTime->format('Y-m-d H:i:s').')');
            }

            $this->memcache->delete($identifier.'_views');
            $this->manager->flush();
        }
    }

    protected function persistPhotoViews()
    {
        $this->output->writeln('Storing photo views');

        $this->storeViews('Photo');
    }

    protected function persistThreadViews()
    {
        $this->output->writeln('Storing thread views');

        $this->storeViews('Thread');
    }

    protected function persistEventViews()
    {
        $this->output->writeln('Storing event views');

        $this->storeViews('Event');
    }

    protected function persistRideViews()
    {
        $this->output->writeln('Storing ride views');

        $this->storeViews('Ride');
    }

    protected function persistCityViews()
    {
        $this->output->writeln('Storing city views');

        $this->storeViews('City');
    }

    protected function persistBlogPostViews()
    {
        $this->output->writeln('Storing blog post views');

        $this->storeViews('BlogPost');
    }
}
