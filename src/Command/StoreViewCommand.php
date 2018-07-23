<?php

namespace App\Command;

use App\Criticalmass\ViewStorage\ViewStoragePersisterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class StoreViewCommand extends Command
{
    /**
     * @var ViewStoragePersisterInterface $viewSotragePersister
     */
    protected $viewStoragePersister;

    public function __construct(ViewStoragePersisterInterface $viewStoragePersister)
    {
        $this->viewStoragePersister = $viewStoragePersister;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('criticalmass:storeviews')
            ->setDescription('Store saved views');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->viewStoragePersister
            ->setOutput($output)
            ->persistViews();
    }
}
