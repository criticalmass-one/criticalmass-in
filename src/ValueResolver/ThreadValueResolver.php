<?php declare(strict_types=1);

namespace App\ValueResolver;

use App\Entity\Thread;
use App\EntityInterface\PostableInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ThreadValueResolver implements ValueResolverInterface
{
    public function __construct(
        private readonly ManagerRegistry $registry
    ) {

    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== Thread::class && $argument->getType() !== PostableInterface::class) {
            return [];
        }

        $thread = $this->registry->getRepository(Thread::class)->findOneBySlug($request->get('threadSlug'));

        if (!$thread && !$argument->isNullable()) {
            throw new NotFoundHttpException('Thead not found');
        }

        return [$thread];
    }
}
