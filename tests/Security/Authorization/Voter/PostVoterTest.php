<?php declare(strict_types=1);

namespace Tests\Security\Authorization\Voter;

use App\Entity\Post;
use App\Entity\User;
use App\Security\Authorization\Voter\PostVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class PostVoterTest extends TestCase
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

    public function testAuthorMayEditTheirOwnPost(): void
    {
        $author = $this->user();
        $post = (new Post())->setUser($author);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            (new PostVoter())->vote($this->token($author), $post, ['edit'])
        );
    }

    public function testStrangerMayNotEditSomeoneElsesPost(): void
    {
        $post = (new Post())->setUser($this->user());

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            (new PostVoter())->vote($this->token($this->user()), $post, ['edit'])
        );
    }

    public function testAdminMayEditAnyPost(): void
    {
        $post = (new Post())->setUser($this->user());

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            (new PostVoter())->vote($this->token($this->user(['ROLE_ADMIN'])), $post, ['edit'])
        );
    }

    public function testAnonymousVisitorMayNotEdit(): void
    {
        $post = (new Post())->setUser($this->user());

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            (new PostVoter())->vote($this->token(null), $post, ['edit'])
        );
    }

    public function testAuthorMayWithdrawAReply(): void
    {
        $author = $this->user();
        $thread = new \App\Entity\Thread();
        $thread->setFirstPost((new Post())->setUser($author));

        $reply = (new Post())->setUser($author);
        $reply->setThread($thread);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            (new PostVoter())->vote($this->token($author), $reply, ['delete'])
        );
    }

    public function testNobodyMayWithdrawTheFirstPostOfAThread(): void
    {
        $author = $this->user();
        $firstPost = (new Post())->setUser($author);

        $thread = new \App\Entity\Thread();
        $thread->setFirstPost($firstPost);
        $firstPost->setThread($thread);

        $voter = new PostVoter();

        // Ohne ersten Beitrag stuende ein Thema ohne Anfang da — dafuer gibt es
        // stattdessen das Zurueckziehen des ganzen Themas.
        self::assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($this->token($author), $firstPost, ['delete']));
        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->token($this->user(['ROLE_ADMIN'])), $firstPost, ['delete'])
        );
    }

    public function testVoterAbstainsOnForeignAttributes(): void
    {
        $post = (new Post())->setUser($this->user());

        // Wichtig: access_decision_manager läuft auf "unanimous" — ein Voter, der sich
        // für fremde Attribute zuständig erklärt, würde andere Entscheidungen kippen.
        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            (new PostVoter())->vote($this->token($this->user()), $post, ['archive'])
        );
    }

    public function testVoterAbstainsOnForeignSubjects(): void
    {
        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            (new PostVoter())->vote($this->token($this->user()), new \stdClass(), ['edit'])
        );
    }
}
