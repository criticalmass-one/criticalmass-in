<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CalderaBundle\Entity\BlogPost;
use Caldera\Bundle\CalderaBundle\Entity\City;
use Caldera\Bundle\CalderaBundle\Entity\Content;
use Caldera\Bundle\CalderaBundle\Entity\Event;
use Caldera\Bundle\CalderaBundle\Entity\Photo;
use Caldera\Bundle\CalderaBundle\Entity\Ride;
use Caldera\Bundle\CalderaBundle\Entity\Thread;
use Caldera\Bundle\CalderaBundle\EntityInterface\ViewableInterface;
use Caldera\Bundle\CalderaBundle\EntityInterface\ViewInterface;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Lsw\MemcacheBundle\Cache\LoggingMemcache;
use Lsw\MemcacheBundle\Cache\LoggingMemcacheInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StoreViewCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager $manager
     */
    protected $manager;

    /**
     * @var LoggingMemcacheInterface $memcache
     */
    protected $memcache;

    /**
     * @var OutputInterface $output
     */
    protected $output;

    protected function configure()
    {
        $this
            ->setName('criticalmass:storeviews')
            ->setDescription('Store saved views')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->manager = $this->getContainer()->get('doctrine')->getManager();
        $this->memcache = $this->getContainer()->get('memcache.criticalmass');
        $this->output = $output;

        $viewStorage = $this->memcache->get('view_storage');
        $this->memcache->delete('view_storage');

        if (!$viewStorage || !is_array($viewStorage) || !count($viewStorage)) {
            $output->writeln('Nothing to store');

            return;
        }

        foreach ($viewStorage as $view) {
            $this->storeView($view);
        }

        $this->manager->flush();
    }

    protected function storeView(array $viewArray)
    {
        $viewClassName = 'Caldera\Bundle\CalderaBundle\Entity\\'.$viewArray['className'].'View';
        $viewMethod = 'set'.$viewArray['className'];

        /** @var ViewableInterface $entity */
        $entity = $this->manager->getRepository('CalderaBundle:'.$viewArray['className'])->find($viewArray['entityId']);

        $viewDateTime = new \DateTime($viewArray['dateTime']);

        $user = null;

        if ($viewArray['userId']) {
            $user = $this->manager->getRepository('CalderaBundle:User')->find($viewArray['userId']);
        }

        /** @var ViewInterface $view */
        $view = new $viewClassName;
        $view->$viewMethod($entity);
        $view->setUser($user);
        $view->setDateTime($viewDateTime);

        $entity->incViews();

        $this->manager->persist($view);
        $this->manager->persist($entity);

        $this->output->writeln(
            sprintf('Stored <comment>%s</comment> <info>#%d</info> view (%s)', $viewArray['className'], $viewArray['entityId'], $viewDateTime->format('Y-m-d H:i:s'))
        );
    }
}
