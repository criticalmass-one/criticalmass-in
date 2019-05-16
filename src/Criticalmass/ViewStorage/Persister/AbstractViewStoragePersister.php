<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\Persister;

use JMS\Serializer\SerializerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

abstract class AbstractViewStoragePersister implements ViewStoragePersisterInterface
{
    /** @var RegistryInterface $registry */
    protected $registry;

    /** @var SerializerInterface $serializer */
    protected $serializer;

    public function __construct(RegistryInterface $registry, SerializerInterface $serializer)
    {
        $this->registry = $registry;
        $this->serializer = $serializer;
    }
}
