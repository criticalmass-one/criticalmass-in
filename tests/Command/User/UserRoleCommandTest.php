<?php declare(strict_types=1);

namespace Tests\Command\User;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class UserRoleCommandTest extends KernelTestCase
{
    private const string EMAIL = 'photodownloader@criticalmass.in';

    /**
     * @param array<string, mixed> $input
     */
    private function runCommand(array $input): CommandTester
    {
        $tester = new CommandTester((new Application(static::bootKernel()))->find('criticalmass:user:role'));
        $tester->execute($input);

        return $tester;
    }

    private function user(): User
    {
        $em = static::getContainer()->get('doctrine')->getManager();
        $em->clear();

        return $em->getRepository(User::class)->findOneBy(['email' => self::EMAIL]);
    }

    public function testGrantsAndRevokesARoleByEmail(): void
    {
        $this->runCommand(['user' => self::EMAIL, '--revoke' => true]);

        $tester = $this->runCommand(['user' => self::EMAIL]);

        self::assertSame(Command::SUCCESS, $tester->getStatusCode());
        self::assertTrue($this->user()->hasRole('ROLE_ADMIN'));

        // Was schon da war, bleibt; ROLE_USER wird nie gespeichert.
        self::assertContains('ROLE_PHOTO_DOWNLOAD', $this->user()->getRoles());

        $this->runCommand(['user' => self::EMAIL, '--revoke' => true]);

        self::assertFalse($this->user()->hasRole('ROLE_ADMIN'));
        self::assertContains('ROLE_PHOTO_DOWNLOAD', $this->user()->getRoles());
    }

    public function testFindsUsersById(): void
    {
        $this->runCommand(['user' => self::EMAIL, '--revoke' => true]);

        $tester = $this->runCommand(['user' => (string) $this->user()->getId(), 'role' => 'role_admin']);

        self::assertSame(Command::SUCCESS, $tester->getStatusCode());
        self::assertTrue($this->user()->hasRole('ROLE_ADMIN'));

        $this->runCommand(['user' => self::EMAIL, '--revoke' => true]);
    }

    public function testRejectsUnknownUsersAndRoleUser(): void
    {
        self::assertSame(Command::FAILURE, $this->runCommand(['user' => 'niemand@example.org'])->getStatusCode());
        self::assertSame(Command::INVALID, $this->runCommand(['user' => self::EMAIL, 'role' => 'ROLE_USER'])->getStatusCode());
        self::assertSame(Command::INVALID, $this->runCommand(['user' => self::EMAIL, 'role' => 'ADMIN'])->getStatusCode());
    }
}
