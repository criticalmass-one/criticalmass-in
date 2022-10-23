<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\Persister;

use App\Criticalmass\ViewStorage\ViewEntityFactory\ViewEntityFactoryInterface;
use JMS\Serializer\SerializerInterface;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractViewStoragePersister implements ViewStoragePersisterInterface
{
    /** @var ManagerRegistry $registry */
    protected $registry;

    /** @var SerializerInterface $serializer */
    protected $serializer;

    /** @var ViewEntityFactoryInterface $viewEntityFactory */
    protected $viewEntityFactory;

    /** @var string $entityNamespace */
    protected $entityNamespace = 'App\\Entity\\';

    public function __construct(ManagerRegistry $registry, SerializerInterface $serializer, ViewEntityFactoryInterface $viewEntityFactory)
    {
        $this->registry = $registry;
        $this->serializer = $serializer;
        $this->viewEntityFactory = $viewEntityFactory;
    }

    public function setEntityNamespace(string $entityNamespace): ViewStoragePersisterInterface
    {
        $this->entityNamespace = $entityNamespace;

        return $this;
    }
}
