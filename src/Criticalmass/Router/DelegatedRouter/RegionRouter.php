<?php declare(strict_types=1);

namespace App\Criticalmass\Router\DelegatedRouter;

use App\Entity\Region;
use App\EntityInterface\RouteableInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RegionRouter extends AbstractDelegatedRouter
{
    /** @param Region $region */
    public function generate(
        RouteableInterface $region,
        string $routeName = null,
        array $parameters = [],
        int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ): string {
        if ($region->getParent() == null) {
            return $this->router->generate('caldera_criticalmass_region_world', [], $referenceType);
        } elseif ($region->getParent()->getParent() == null) {
            return $this->router->generate('caldera_criticalmass_region_world_region_1', [
                'slug1' => $region->getSlug()
            ],
                $referenceType);
        } elseif ($region->getParent()->getParent()->getParent() == null) {
            return $this->router->generate('caldera_criticalmass_region_world_region_2', [
                'slug1' => $region->getParent()->getSlug(),
                'slug2' => $region->getSlug()
            ],
                $referenceType);
        } elseif ($region->getParent()->getParent()->getParent()->getParent() == null) {
            return $this->router->generate('caldera_criticalmass_region_world_region_3', [
                'slug1' => $region->getParent()->getParent()->getSlug(),
                'slug2' => $region->getParent()->getSlug(),
                'slug3' => $region->getSlug()
            ],
                $referenceType);
        }
    }
}
