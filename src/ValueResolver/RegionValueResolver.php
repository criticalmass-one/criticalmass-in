<?php declare(strict_types=1);

namespace App\ValueResolver;

use App\Entity\Region;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RegionValueResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry
    ) {

    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== Region::class
            || $argument->getName() !== 'region'
            || !$request->query->has('regionSlug')
        ) {
            return [];
        }

        $slug = $request->query->get('regionSlug');

        if (!$slug) {
            return [];
        }

        $region = $this->managerRegistry
            ->getRepository(Region::class)
            ->findOneBy(['slug' => $slug])
        ;

        if (!$region) {
            throw new NotFoundHttpException(sprintf('Region with slug "%s" not found.', $slug));
        }

        return [$region];
    }
}
