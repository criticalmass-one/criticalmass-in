<?php declare(strict_types=1);

namespace App\Command\Cycles;

use App\Entity\CityCycle;
use App\Entity\CitySlug;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[AsCommand(
    name: 'criticalmass:cycles:list',
    description: 'List all cycles for a city',
)]
class ListCyclesCommand extends Command
{
    public function __construct(protected ManagerRegistry $registry, protected TranslatorInterface $translator)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('citySlug', InputArgument::REQUIRED,'City slug');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $citySlug */
        $citySlugString = $input->getArgument('citySlug');

        /** @var CitySlug $citySlug */
        $citySlug = $this->registry->getRepository(CitySlug::class)->findOneBySlug($citySlugString);

        if (!$citySlug) {
            $output->writeln(sprintf('No city found with slug "%"', $citySlugString));
        }

        $city = $citySlug->getCity();

        $cycleList = $this->registry->getRepository(CityCycle::class)->findByCity($city);

        $table = new Table($output);
        $table->setHeaders(['Id', 'Location', 'Day', 'Week', 'UTC Time', 'Valid from', 'Valid until']);

        $utc = new \DateTimeZone('UTC');

        /** @var CityCycle $cityCycle */
        foreach ($cycleList as $cityCycle) {
            $table->addRow([
                $cityCycle->getId(),
                sprintf('%s (%f, %f)', $cityCycle->getLocation(), $cityCycle->getLatitude(), $cityCycle->getLongitude()),
                $this->translator->trans(sprintf('cycle.event_date.day.%d', $cityCycle->getDayOfWeek())),
                $this->translator->trans(sprintf('cycle.event_date.month_week.%d', $cityCycle->getWeekOfMonth())),
                $cityCycle->getTime() ? $cityCycle->getTime()->format('H:i') : '',
                $cityCycle->getValidFrom() ? $cityCycle->getValidFrom()->format('Y-m-d') : '',
                $cityCycle->getValidUntil() ? $cityCycle->getValidUntil()->format('Y-m-d') : '',
            ]);
        }

        $table->render();

        return Command::SUCCESS;
    }
}
