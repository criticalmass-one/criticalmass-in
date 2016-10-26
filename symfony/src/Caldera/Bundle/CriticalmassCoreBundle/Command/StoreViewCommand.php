<?php

namespace Caldera\Bundle\CriticalmassCoreBundle\Command;

use Caldera\Bundle\CalderaBundle\EntityInterface\ViewableInterface;
use Caldera\Bundle\CalderaBundle\EntityInterface\ViewInterface;
use Caldera\Bundle\CalderaBundle\ViewStorage\ViewStoragePersister;
use Doctrine\ORM\EntityManager;
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
            ->setDescription('Store saved views');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var ViewStoragePersisterInterface $persister */
        $persister = $this->getContainer()->get('caldera.view_storage.persister');

        $persister
            ->setOutput($output)
            ->persistViews();
    }
}
