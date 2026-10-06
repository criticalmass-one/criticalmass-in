<?php declare(strict_types=1);

namespace Tests\Security\Authorization\Voter;

use App\Entity\Post;
use App\Entity\Thread;
use App\Entity\User;
use App\Security\Authorization\Voter\ThreadVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class ThreadVoterTest extends TestCase
{
    private function token(?User $user): TokenInterface
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        return $token;
    }

    /**
     * @param list<string> $roles
     */
    private function user(array $roles = []): User
    {
        return (new User())->setRoles($roles);
    }

    private function threadOpenedBy(?User $author): Thread
    {
        $thread = new Thread();

        if (null !== $author) {
            $thread->setFirstPost((new Post())->setUser($author));
        }

        return $thread;
    }

    public function testThreadOpenerMayEditTheTitle(): void
    {
        $opener = $this->user();

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            (new ThreadVoter())->vote($this->token($opener), $this->threadOpenedBy($opener), ['edit'])
        );
    }

    public function testSomeoneWhoOnlyRepliedMayNotEditTheTitle(): void
    {
        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            (new ThreadVoter())->vote($this->token($this->user()), $this->threadOpenedBy($this->user()), ['edit'])
        );
    }

    public function testAdminMayEditAnyTitle(): void
    {
        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            (new ThreadVoter())->vote($this->token($this->user(['ROLE_ADMIN'])), $this->threadOpenedBy($this->user()), ['edit'])
        );
    }

    public function testThreadWithoutFirstPostIsEditableByAdminsOnly(): void
    {
        // Ältere Threads können ohne firstPost in der Datenbank liegen.
        $voter = new ThreadVoter();

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->token($this->user()), $this->threadOpenedBy(null), ['edit'])
        );
        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token($this->user(['ROLE_ADMIN'])), $this->threadOpenedBy(null), ['edit'])
        );
    }

    public function testAnonymousVisitorMayNotEdit(): void
    {
        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            (new ThreadVoter())->vote($this->token(null), $this->threadOpenedBy($this->user()), ['edit'])
        );
    }

    public function testVoterAbstainsOnForeignAttributes(): void
    {
        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            (new ThreadVoter())->vote($this->token($this->user()), $this->threadOpenedBy($this->user()), ['archive'])
        );
    }

    public function testOnlyAdminsMayLockAThread(): void
    {
        $opener = $this->user();
        $thread = $this->threadOpenedBy($opener);
        $voter = new ThreadVoter();

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->token($opener), $thread, ['lock']),
            'Auch wer das Thema eröffnet hat, schließt es nicht selbst.'
        );
        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token($this->user(['ROLE_ADMIN'])), $thread, ['lock'])
        );
    }

    public function testOnlyAdminsMayMoveAThread(): void
    {
        $opener = $this->user();
        $thread = $this->threadOpenedBy($opener);
        $voter = new ThreadVoter();

        self::assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($this->token($opener), $thread, ['move']));
        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token($this->user(['ROLE_ADMIN'])), $thread, ['move'])
        );
    }

    public function testThreadOpenerAndAdminMayWithdrawTheThread(): void
    {
        $opener = $this->user();
        $thread = $this->threadOpenedBy($opener);
        $voter = new ThreadVoter();

        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($this->token($opener), $thread, ['delete']));
        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token($this->user(['ROLE_ADMIN'])), $thread, ['delete'])
        );
        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->token($this->user()), $thread, ['delete'])
        );
    }

    public function testOnlyAdminsMayPinAThread(): void
    {
        $opener = $this->user();
        $thread = $this->threadOpenedBy($opener);
        $voter = new ThreadVoter();

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->token($opener), $thread, ['pin'])
        );
        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->token($this->user(['ROLE_ADMIN'])), $thread, ['pin'])
        );
    }
}
