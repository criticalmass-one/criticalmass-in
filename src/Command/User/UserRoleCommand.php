<?php declare(strict_types=1);

namespace App\Command\User;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Vergibt oder entzieht eine Rolle.
 *
 * Eine Oberflaeche dafuer gibt es nicht. Wer Meldungen bearbeiten soll, braucht
 * ROLE_ADMIN — und Benutzernamen sind nicht eindeutig, deshalb geht der Befehl
 * ueber die Kontonummer oder die E-Mail-Adresse.
 */
#[AsCommand(
    name: 'criticalmass:user:role',
    description: 'Grant or revoke a role (e.g. ROLE_ADMIN) for a user given by id or email',
)]
class UserRoleCommand extends Command
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly ManagerRegistry $registry
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('user', InputArgument::REQUIRED, 'Kontonummer oder E-Mail-Adresse')
            ->addArgument('role', InputArgument::OPTIONAL, 'Rolle', 'ROLE_ADMIN')
            ->addOption('revoke', null, InputOption::VALUE_NONE, 'Rolle entziehen statt vergeben');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $identifier = (string) $input->getArgument('user');
        $role = strtoupper((string) $input->getArgument('role'));

        if (!str_starts_with($role, 'ROLE_') || 'ROLE_USER' === $role) {
            $io->error(sprintf('"%s" ist keine Rolle, die sich vergeben laesst.', $role));

            return Command::INVALID;
        }

        $user = ctype_digit($identifier)
            ? $this->userRepository->find((int) $identifier)
            : $this->userRepository->findOneBy(['email' => $identifier]);

        if (!$user instanceof User) {
            $io->error(sprintf('Kein Konto zu "%s" gefunden.', $identifier));

            return Command::FAILURE;
        }

        // getRoles() fuegt ROLE_USER immer an; gespeichert wird nur, was darueber hinausgeht.
        $roles = array_values(array_diff($user->getRoles(), ['ROLE_USER']));

        $roles = $input->getOption('revoke')
            ? array_values(array_diff($roles, [$role]))
            : array_values(array_unique([...$roles, $role]));

        $user->setRoles($roles);
        $this->registry->getManager()->flush();

        $io->success(sprintf(
            '%s (#%d, %s) hat jetzt: %s',
            $user->getUsername(),
            $user->getId(),
            $user->getEmail(),
            [] === $roles ? 'keine besonderen Rollen' : implode(', ', $roles)
        ));

        return Command::SUCCESS;
    }
}
