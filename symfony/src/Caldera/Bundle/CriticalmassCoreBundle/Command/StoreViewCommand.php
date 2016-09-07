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
        $this->persistBlogPostViews($output);
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

    protected function storeViews(OutputInterface $output, string $identifier, string $entityClassName, string $storageClassName)
    {
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
            }

            $this->memcache->delete($identifier.'_views');
            $this->manager->flush();
        }
    }

    protected function persistPhotoViews(OutputInterface $output)
    {
        $output->writeln('Storing photo views');

        $this->storeViews($output, 'photo', 'Photo', 'Caldera\Bundle\CalderaBundle\Entity\PhotoView');
    }

    protected function persistThreadViews(OutputInterface $output)
    {
        $output->writeln('Storing thread views');

        $this->storeViews($output, 'thread', 'Thread', 'Caldera\Bundle\CalderaBundle\Entity\ThreadView');
    }

    protected function persistEventViews(OutputInterface $output)
    {
        $output->writeln('Storing event views');

        $this->storeViews($output, 'event', 'Event', 'Caldera\Bundle\CalderaBundle\Entity\EventView');
    }

    protected function persistRideViews(OutputInterface $output)
    {
        $output->writeln('Storing ride views');

        $this->storeViews($output, 'ride', 'Ride', 'Caldera\Bundle\CalderaBundle\Entity\RideView');
    }

    protected function persistCityViews(OutputInterface $output)
    {
        $output->writeln('Storing city views');

        $this->storeViews($output, 'city', 'City', 'Caldera\Bundle\CalderaBundle\Entity\CityView');
    }

    protected function persistBlogPostViews(OutputInterface $output)
    {
        $output->writeln('Storing blog post views');

        $this->storeViews($output, 'blogPost', 'BlogPost', 'Caldera\Bundle\CalderaBundle\Entity\BlogPostView');
    }
}
