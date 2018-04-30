<?php declare(strict_types=1);

namespace Criticalmass\Bundle\AppBundle\Command;

use Criticalmass\Bundle\AppBundle\Entity\User;
use Criticalmass\Component\ProfilePhotoGenerator\ProfilePhotoGenerator;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateProfilePhotosCommand extends Command
{
    protected $registry;

    protected $profilePhotoGenerator;

    public function __construct(Registry $registry, ProfilePhotoGenerator $profilePhotoGenerator)
    {
        $this->registry = $registry;
        $this->profilePhotoGenerator = $profilePhotoGenerator;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('criticalmass:profile-photo:generate')
            ->setDescription('Generate profile photos');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $userList = $this->registry->getRepository(User::class)->findAll();

        $progress = new ProgressBar($output, count($userList));
        $table = new Table($output);

        /** @var User $user */
        foreach ($userList as $user) {
            //if (!$user->getImageName()) {
                $filename = $this->profilePhotoGenerator->setUser($user)->generate();

                $table->addRow([$user->getUsername(), $filename]);
                $progress->advance();
            //}
        }

        $progress->finish();
        $table->render();
    }
}
