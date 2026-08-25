<?php declare(strict_types=1);

namespace Tests\Security\Voter;

use App\Entity\Track;
use App\Entity\User;
use App\Security\Authorization\Voter\TrackVoter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class TrackVoterTest extends TestCase
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
        yield 'view' => ['view'];
        yield 'download' => ['download'];
        yield 'approve' => ['approve'];
        yield 'edit' => ['edit'];
    }

    #[Test]
    #[DataProvider('attributes')]
    public function everyAttributeIsRestrictedToOwnerOrAdmin(string $attribute): void
    {
        $owner = new User();
        $track = (new Track())->setUser($owner);
        $voter = new TrackVoter();

        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($this->token($owner), $track, [$attribute]));
        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($this->token((new User())->setRoles(['ROLE_ADMIN'])), $track, [$attribute]));
        self::assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($this->token(new User()), $track, [$attribute]));
    }

    #[Test]
    public function abstainsOnUnsupportedAttribute(): void
    {
        $owner = new User();
        $track = (new Track())->setUser($owner);

        self::assertSame(VoterInterface::ACCESS_ABSTAIN, (new TrackVoter())->vote($this->token($owner), $track, ['delete']));
    }

    #[Test]
    public function trackWithoutOwnerIsOnlyAccessibleToAdmins(): void
    {
        $track = new Track();
        $voter = new TrackVoter();

        self::assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($this->token(new User()), $track, ['view']));
        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($this->token((new User())->setRoles(['ROLE_ADMIN'])), $track, ['view']));
    }
}
