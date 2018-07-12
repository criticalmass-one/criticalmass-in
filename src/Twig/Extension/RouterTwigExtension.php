<?php declare(strict_types=1);

namespace App\Twig\Extension;

use App\Criticalmass\Router\ObjectRouterInterface;
use App\EntityInterface\RouteableInterface;

class RouterTwigExtension extends \Twig_Extension
{
    /** @var ObjectRouterInterface $router */
    protected $router;

    public function __construct(ObjectRouterInterface $router)
    {
        $this->router = $router;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_SimpleFunction('objectPath', [$this, 'objectPath'], [
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
