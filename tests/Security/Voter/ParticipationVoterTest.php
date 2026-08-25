<?php declare(strict_types=1);

namespace Tests\Security\Voter;

use App\Entity\Participation;
use App\Entity\User;
use App\Security\Authorization\Voter\ParticipationVoter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class ParticipationVoterTest extends TestCase
{
    private function token(?object $user): TokenInterface
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        return $token;
    }

    /**
     * @return iterable<string, array{0: string}>
     */
    public static function attributes(): iterable
    {
        yield 'cancel' => ['cancel'];
        yield 'delete' => ['delete'];
    }

    #[Test]
    #[DataProvider('attributes')]
    public function ownerMayCancelAndDelete(string $attribute): void
    {
        $owner = new User();
        $participation = (new Participation())->setUser($owner);

        $result = (new ParticipationVoter())->vote($this->token($owner), $participation, [$attribute]);

        self::assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    #[Test]
    #[DataProvider('attributes')]
    public function adminMayCancelAndDeleteForeignParticipation(string $attribute): void
    {
        $admin = (new User())->setRoles(['ROLE_ADMIN']);
        $participation = (new Participation())->setUser(new User());

        $result = (new ParticipationVoter())->vote($this->token($admin), $participation, [$attribute]);

        self::assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    #[Test]
    public function strangerIsDenied(): void
    {
        $participation = (new Participation())->setUser(new User());

        $result = (new ParticipationVoter())->vote($this->token(new User()), $participation, ['cancel']);

        self::assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    #[Test]
    public function anonymousTokenIsDenied(): void
    {
        $participation = (new Participation())->setUser(new User());

        $result = (new ParticipationVoter())->vote($this->token(null), $participation, ['cancel']);

        self::assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    #[Test]
    public function abstainsOnUnknownAttribute(): void
    {
        $owner = new User();
        $participation = (new Participation())->setUser($owner);

        $result = (new ParticipationVoter())->vote($this->token($owner), $participation, ['edit']);

        self::assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    #[Test]
    public function attributesAreCaseSensitiveDespiteLowercasedMethodLookup(): void
    {
        // supports() compares against the lcfirst()ed can*() names, so an upper-case
        // attribute never reaches voteOnAttribute() (which would have lower-cased it).
        $owner = new User();
        $participation = (new Participation())->setUser($owner);

        $result = (new ParticipationVoter())->vote($this->token($owner), $participation, ['CANCEL']);

        self::assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }

    #[Test]
    public function abstainsOnForeignSubject(): void
    {
        $owner = new User();

        $result = (new ParticipationVoter())->vote($this->token($owner), new \stdClass(), ['cancel']);

        self::assertSame(VoterInterface::ACCESS_ABSTAIN, $result);
    }
}
