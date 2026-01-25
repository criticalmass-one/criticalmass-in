<?php declare(strict_types=1);

namespace App\GraphQL\Resolver;

use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractResolver
{
    protected ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }
}
