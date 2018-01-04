<?php

namespace Criticalmass\Bundle\AppBundle\Command;

use Criticalmass\Component\ViewStorage\ViewStoragePersisterInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StoreViewCommand extends ContainerAwareCommand
{
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
        $persister = $this->getContainer()->get('Criticalmass\Component\ViewStorage\ViewStoragePersister');

        $persister
            ->setOutput($output)
            ->persistViews();
    }
}
