<?php

namespace AppBundle\Command;

use AppBundle\ViewStorage\ViewStoragePersisterInterface;
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
        $persister = $this->getContainer()->get('caldera.view_storage.persister');

        $persister
            ->setOutput($output)
            ->persistViews();
    }
}
