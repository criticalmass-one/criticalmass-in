<?php declare(strict_types=1);

namespace Tests\Twig;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\Entity\City;
use App\Twig\Extension\RouterTwigExtension;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RouterTwigExtensionTest extends TestCase
{
    #[Test]
    public function objectPathDelegatesToObjectRouterWithDefaults(): void
    {
        $city = new City();

        $router = $this->createMock(ObjectRouterInterface::class);
        $router->expects(self::once())
            ->method('generate')
            ->with($city, null, [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/hamburg');

        self::assertSame('/hamburg', (new RouterTwigExtension($router))->objectPath($city));
    }

    #[Test]
    public function objectPathPassesRouteNameAndParameters(): void
    {
        $city = new City();

        $router = $this->createMock(ObjectRouterInterface::class);
        $router->expects(self::once())
            ->method('generate')
            ->with($city, 'caldera_criticalmass_city_show', ['page' => 2], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/hamburg?page=2');

        self::assertSame('/hamburg?page=2', (new RouterTwigExtension($router))->objectPath($city, 'caldera_criticalmass_city_show', ['page' => 2]));
    }

    #[Test]
    public function registersObjectPathFunction(): void
    {
        $functions = (new RouterTwigExtension($this->createMock(ObjectRouterInterface::class)))->getFunctions();

        self::assertCount(1, $functions);
        self::assertSame('object_path', $functions[0]->getName());
    }
}
