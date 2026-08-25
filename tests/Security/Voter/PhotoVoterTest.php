<?php declare(strict_types=1);

namespace Tests\Security\Voter;

use App\Entity\Photo;
use App\Entity\User;
use App\Security\Authorization\Voter\PhotoVoter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class PhotoVoterTest extends TestCase
{
    private function token(?object $user): TokenInterface
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        return $token;
    }

    #[Test]
    public function anyLoggedInUserMayViewAndUpload(): void
    {
        $photo = (new Photo())->setUser(new User());
        $voter = new PhotoVoter();

        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($this->token(new User()), $photo, ['view']));
        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($this->token(new User()), $photo, ['upload']));
    }

    #[Test]
    public function onlyOwnerOrAdminMayEdit(): void
    {
        $owner = new User();
        $photo = (new Photo())->setUser($owner);
        $voter = new PhotoVoter();

        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($this->token($owner), $photo, ['edit']));
        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($this->token((new User())->setRoles(['ROLE_ADMIN'])), $photo, ['edit']));
        self::assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($this->token(new User()), $photo, ['edit']));
    }

    #[Test]
    public function viewIsDeniedWithoutAuthenticatedUser(): void
    {
        $photo = (new Photo())->setUser(new User());

        self::assertSame(VoterInterface::ACCESS_DENIED, (new PhotoVoter())->vote($this->token(null), $photo, ['view']));
    }

    #[Test]
    public function abstainsForNonPhotoSubjects(): void
    {
        self::assertSame(VoterInterface::ACCESS_ABSTAIN, (new PhotoVoter())->vote($this->token(new User()), new User(), ['view']));
    }
}
