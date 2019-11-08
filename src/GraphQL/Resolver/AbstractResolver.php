<?php declare(strict_types=1);

namespace App\GraphQL\Resolver;

use Symfony\Bridge\Doctrine\RegistryInterface;

abstract class AbstractResolver
{
    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }
}
