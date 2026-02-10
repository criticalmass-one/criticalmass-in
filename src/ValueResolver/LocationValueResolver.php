<?php declare(strict_types=1);

namespace App\ValueResolver;

use App\Entity\CitySlug;
use App\Entity\Location;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LocationValueResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly ManagerRegistry $registry
    ) {

    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (
            $argument->getType() !== Location::class
            || $argument->getName() !== 'location'
        ) {
            return [];
        }

        $citySlugParam = $request->attributes->get('citySlug');
        $slug = $request->attributes->get('slug');

        if (!$citySlugParam || !$slug) {
            return [];
        }

        $citySlug = $this->registry->getRepository(CitySlug::class)->findOneBySlug($citySlugParam);

        if (!$citySlug) {
            throw new NotFoundHttpException('City not found');
        }

        $city = $citySlug->getCity();

        $location = $this->registry->getRepository(Location::class)->findOneByCityAndSlug($city, $slug);

        if (!$location && !$argument->isNullable()) {
            throw new NotFoundHttpException('Location not found');
        }

        return [$location];
    }
}
