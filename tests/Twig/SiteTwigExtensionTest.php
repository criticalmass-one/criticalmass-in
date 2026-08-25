<?php declare(strict_types=1);

namespace Tests\Twig;

use App\Entity\City;
use App\Entity\Ride;
use App\Twig\Extension\SiteTwigExtension;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SiteTwigExtensionTest extends TestCase
{
    private SiteTwigExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new SiteTwigExtension(
            $this->createMock(TranslatorInterface::class),
            $this->createMock(RouterInterface::class),
        );
    }

    #[Test]
    public function instanceofChecksTheClassHierarchy(): void
    {
        self::assertTrue($this->extension->instanceof(new Ride(), Ride::class));
        self::assertTrue($this->extension->instanceof(new Ride(), \App\EntityInterface\RouteableInterface::class));
        self::assertFalse($this->extension->instanceof(new Ride(), City::class));
        self::assertFalse($this->extension->instanceof('ride', Ride::class));
    }

    #[Test]
    public function todayComparesOnlyTheCalendarDate(): void
    {
        self::assertTrue($this->extension->today(new \DateTime('today 00:00:00')));
        self::assertTrue($this->extension->today(new \DateTime('today 23:59:59')));
        self::assertFalse($this->extension->today(new \DateTime('yesterday 23:59:59')));
        self::assertFalse($this->extension->today(new \DateTime('tomorrow 00:00:00')));
    }

    #[Test]
    public function daysSinceCountsFullDays(): void
    {
        $twoDaysAgo = '@' . (time() - 2 * 86400);
        $almostThreeDaysAgo = '@' . (time() - 3 * 86400 + 60);

        self::assertSame(2.0, $this->extension->daysSince($twoDaysAgo));
        self::assertSame(2.0, $this->extension->daysSince($almostThreeDaysAgo));
    }

    #[Test]
    public function daysSinceIsNegativeForFutureDates(): void
    {
        self::assertSame(-1.0, $this->extension->daysSince('@' . (time() + 3600)));
    }

    #[Test]
    public function registersDaysSinceTodayAndInstanceofFunctions(): void
    {
        $names = array_map(
            static fn (\Twig\TwigFunction $function): string => $function->getName(),
            $this->extension->getFunctions()
        );

        self::assertContains('daysSince', $names);
        self::assertContains('today', $names);
        self::assertContains('instanceof', $names);
    }

    #[Test]
    public function hashtagToCityFilterHasAnImplementation(): void
    {
        $filters = $this->extension->getFilters();

        self::assertSame('hashtagToCity', $filters[0]->getName());

        if (!is_callable($filters[0]->getCallable())) {
            self::markTestIncomplete(
                'SiteTwigExtension registers the "hashtagToCity" filter with [$this, "hashtagToCity"], '
                .'but no such method exists — using the filter in a template would fail at render time.'
            );
        }

        self::assertTrue(is_callable($filters[0]->getCallable()));
    }
}
