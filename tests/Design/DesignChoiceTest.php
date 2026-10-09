<?php declare(strict_types=1);

namespace Tests\Design;

use App\Design\DesignChoice;
use App\Entity\User;
use Flagception\Manager\FeatureManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class DesignChoiceTest extends TestCase
{
    private function choice(bool $flag, ?Request $request, ?User $user = null): DesignChoice
    {
        $requestStack = new RequestStack();

        if ($request) {
            $requestStack->push($request);
        }

        $security = $this->createStub(Security::class);
        $security->method('getUser')->willReturn($user);

        $featureManager = $this->createStub(FeatureManagerInterface::class);
        $featureManager->method('isActive')->willReturnCallback(
            fn (string $name): bool => DesignChoice::FEATURE === $name && $flag
        );

        return new DesignChoice($requestStack, $security, $featureManager);
    }

    private function requestWithCookie(?string $value): Request
    {
        return new Request(cookies: null === $value ? [] : [DesignChoice::COOKIE => $value]);
    }

    public function testFlagOffAlwaysGivesOldDesign(): void
    {
        $user = (new User())->setDesignV2(true);

        $choice = $this->choice(false, $this->requestWithCookie('v2'), $user);

        self::assertFalse($choice->isAvailable());
        self::assertFalse($choice->isV2());
    }

    public function testNoRequestGivesOldDesign(): void
    {
        self::assertFalse($this->choice(true, null)->isV2());
    }

    public function testGuestFollowsCookie(): void
    {
        self::assertTrue($this->choice(true, $this->requestWithCookie('v2'))->isV2());
        self::assertFalse($this->choice(true, $this->requestWithCookie('v1'))->isV2());
        self::assertFalse($this->choice(true, $this->requestWithCookie(null))->isV2());
        self::assertFalse($this->choice(true, $this->requestWithCookie('quatsch'))->isV2());
    }

    public function testAccountChoiceWinsOverCookie(): void
    {
        $optedOut = (new User())->setDesignV2(false);
        $optedIn = (new User())->setDesignV2(true);

        self::assertFalse($this->choice(true, $this->requestWithCookie('v2'), $optedOut)->isV2());
        self::assertTrue($this->choice(true, $this->requestWithCookie('v1'), $optedIn)->isV2());
    }

    public function testAccountWithoutChoiceFollowsCookie(): void
    {
        $undecided = new User();

        self::assertNull($undecided->getDesignV2());
        self::assertTrue($this->choice(true, $this->requestWithCookie('v2'), $undecided)->isV2());
        self::assertFalse($this->choice(true, $this->requestWithCookie(null), $undecided)->isV2());
    }

    public function testDecisionIsTakenAgainForANewRequest(): void
    {
        $requestStack = new RequestStack();
        $featureManager = $this->createStub(FeatureManagerInterface::class);
        $featureManager->method('isActive')->willReturn(true);

        $choice = new DesignChoice($requestStack, $this->createStub(Security::class), $featureManager);

        $requestStack->push($this->requestWithCookie('v2'));
        self::assertTrue($choice->isV2());

        $requestStack->pop();
        $requestStack->push($this->requestWithCookie('v1'));
        self::assertFalse($choice->isV2());
    }
}
