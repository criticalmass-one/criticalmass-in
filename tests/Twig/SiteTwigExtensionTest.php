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
    public function registersTheDaysSinceFunction(): void
    {
        $names = array_map(static fn (\Twig\TwigFunction $f) => $f->getName(), $this->extension->getFunctions());

        self::assertSame(['daysSince'], $names);
    }

    #[Test]
    public function registersNoFiltersAndEveryFunctionIsCallable(): void
    {
        self::assertSame([], $this->extension->getFilters());

        foreach ($this->extension->getFunctions() as $function) {
            self::assertTrue(is_callable($function->getCallable()), $function->getName() . ' has no implementation');
        }
    }
}
