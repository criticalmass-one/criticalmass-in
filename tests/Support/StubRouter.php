<?php declare(strict_types=1);

namespace Tests\Support;

use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RouterInterface;

/**
 * Minimal RouterInterface over an in-memory RouteCollection, so the custom entity router
 * can be exercised without booting the kernel.
 */
final class StubRouter implements RouterInterface
{
    private RouteCollection $routes;
    private RequestContext $context;
    private UrlGeneratorInterface $generator;

    /**
     * @param array<string, string> $routes route name => path
     */
    public function __construct(array $routes)
    {
        $this->routes = new RouteCollection();

        foreach ($routes as $name => $path) {
            $this->routes->add($name, new Route($path));
        }

        $this->context = new RequestContext('', 'GET', 'criticalmass.in', 'https');
        $this->generator = new UrlGenerator($this->routes, $this->context);
    }

    public function setContext(RequestContext $context): void
    {
        $this->context = $context;
        $this->generator->setContext($context);
    }

    public function getContext(): RequestContext
    {
        return $this->context;
    }

    public function getRouteCollection(): RouteCollection
    {
        return $this->routes;
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        return $this->generator->generate($name, $parameters, $referenceType);
    }

    /**
     * @return array<string, mixed>
     */
    public function match(string $pathinfo): array
    {
        throw new \LogicException('Matching is not supported by the stub router');
    }
}
