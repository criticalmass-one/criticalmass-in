<?php declare(strict_types=1);

namespace App\ValueResolver;

use App\Entity\City;
use App\Entity\CitySlug;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CityValueResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly ManagerRegistry $registry
    ) {

    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== City::class
            || $argument->getName() !== 'city'
            || !$request->query->has('citySlug')
        ) {
            return [];
        }

        $citySlug = $this->registry->getRepository(CitySlug::class)->findOneBySlug(
            $request->get('citySlug')
        );

        if (!$citySlug && $argument->isNullable()) {
            return [];
        }

        if (!$citySlug && !$argument->isNullable()) {
            throw new NotFoundHttpException('City not found');
        }

        $city = $citySlug->getCity();

        if (!$city && !$argument->isNullable()) {
            throw new NotFoundHttpException('Ride not found');
        }

        return [$city];
    }
}
