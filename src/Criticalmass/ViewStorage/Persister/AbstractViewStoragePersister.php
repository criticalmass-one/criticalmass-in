<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\Persister;

use App\Criticalmass\ViewStorage\ViewEntityFactory\ViewEntityFactoryInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\SerializerInterface;

abstract class AbstractViewStoragePersister implements ViewStoragePersisterInterface
{
    protected string $entityNamespace = 'App\\Entity\\';

    public function __construct(
        protected readonly ManagerRegistry $registry,
        protected readonly SerializerInterface $serializer,
        protected readonly ViewEntityFactoryInterface $viewEntityFactory
    )
    {

    }

    public function setEntityNamespace(string $entityNamespace): ViewStoragePersisterInterface
    {
        $this->entityNamespace = $entityNamespace;

        return $this;
    }
}
