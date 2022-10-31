<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\Persister;

use App\Criticalmass\ViewStorage\ViewEntityFactory\ViewEntityFactoryInterface;
use JMS\Serializer\SerializerInterface;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractViewStoragePersister implements ViewStoragePersisterInterface
{
    /** @var string $entityNamespace */
    protected $entityNamespace = 'App\\Entity\\';

    public function __construct(protected ManagerRegistry $registry, protected SerializerInterface $serializer, protected ViewEntityFactoryInterface $viewEntityFactory)
    {
    }

    public function setEntityNamespace(string $entityNamespace): ViewStoragePersisterInterface
    {
        $this->entityNamespace = $entityNamespace;

        return $this;
    }
}
