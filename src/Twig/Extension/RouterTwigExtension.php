<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\EntityInterface\RouteableInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RouterTwigExtension extends AbstractExtension
{
    public function __construct(protected ObjectRouterInterface $router)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('objectPath', $this->objectPath(...), [
                'is_safe' => ['raw'],
            ]),
        ];
    }

    public function objectPath(RouteableInterface $object, string $routeName = null, array $parameters = []): string
    {
        return $this->router->generate($object, $routeName, $parameters);
    }

    public function getName(): string
    {
        return 'router_extension';
    }
}
