<?php declare(strict_types=1);

namespace App\Command\Facebook;

use App\Entity\Ride;
use App\Criticalmass\Facebook\EventSelector;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AutoSelectRideEventCommand extends Command
{
    /** @var EventSelector $eventSelector */
    protected $eventSelector;

    public function __construct(EventSelector $eventSelector)
    {
        $this->eventSelector = $eventSelector;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('criticalmass:facebook:autoselectrideevent')
            ->setDescription('');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->eventSelector->autoselect();

        $table = new Table($output);
        $table
            ->setHeaders(['City', 'DateTime', 'EventId']);

        $assignedRides = $this->eventSelector->getAssignedRides();

        /** @var Ride $ride */
        foreach ($assignedRides as $ride) {
            $table
                ->addRow([
                    $ride->getCity()->getCity(),
                    $ride->getDateTime()->format('Y-m-d H:i'),
                    $ride->getFacebook()
                ]);
        }

        $table->render();
    }
}
