<?php

namespace Criticalmass\Bundle\AppBundle\Command\Facebook;

use Criticalmass\Bundle\AppBundle\Entity\FacebookRideProperties;
use Criticalmass\Component\Facebook\EventPropertyReader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RidePropertiesCommand extends Command
{
    /** @var EventPropertyReader $eventPropertyReader */
    protected $eventPropertyReader;

    public function __construct(EventPropertyReader $eventPropertyReader)
    {
        $this->eventPropertyReader = $eventPropertyReader;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('criticalmass:facebook:rideproperties')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->eventPropertyReader->read();

        $table = new Table($output);
        $table
            ->setHeaders(['City', 'Title', 'Attendings'])
        ;

        $properties = $this->eventPropertyReader->getPropertyList();

        /** @var FacebookRideProperties $property */
        foreach ($properties as $property) {
            $table
                ->addRow([
                    $property->getRide()->getCity()->getCity(),
                    $property->getName(),
                    $property->getNumberAttending(),
                ]);
        }

        $table->render();
    }
}
