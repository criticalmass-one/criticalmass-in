<?php declare(strict_types=1);

namespace App\Criticalmass\Router\DelegatedRouter;

use App\Entity\Region;
use App\EntityInterface\RouteableInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegionRouter extends AbstractDelegatedRouter
{
    protected static function getEntityFqcn(): string
    {
        return Region::class;
    }

    /** @param Region $region */
    public function generate(
        RouteableInterface $region,
        ?string $routeName = null,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        $slugs = [];
        $current = $region;

        while ($current->getParent() !== null) {
            array_unshift($slugs, $current->getSlug());
            $current = $current->getParent();
        }

        $depth = count($slugs);

        if ($depth === 0) {
            return $this->router->generate('caldera_criticalmass_region_world', [], $referenceType);
        }

        $routeName = sprintf('caldera_criticalmass_region_world_region_%d', $depth);
        $routeParameters = [];

        foreach ($slugs as $index => $slug) {
            $routeParameters[sprintf('slug%d', $index + 1)] = $slug;
        }

        return $this->router->generate($routeName, $routeParameters, $referenceType);
    }
}
