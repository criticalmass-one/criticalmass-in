<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\Cache;

use App\Criticalmass\ViewStorage\Persister\ViewStoragePersisterInterface;
use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use App\Criticalmass\ViewStorage\ViewModel\ViewFactory;
use JMS\Serializer\SerializerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PhpAmqpLib\Exception\AMQPIOException;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RobustViewStorageCache extends ViewStorageCache
{
    /** @var ViewStoragePersisterInterface $viewStoragePersister */
    protected $viewStoragePersister;

    /** @var RegistryInterface $registry */
    protected $registry;

    public function __construct(RegistryInterface $registry, ViewStoragePersisterInterface $viewStoragePersister, TokenStorageInterface $tokenStorage, ProducerInterface $producer, SerializerInterface $serializer)
    {
        $this->viewStoragePersister = $viewStoragePersister;
        $this->registry = $registry;

        parent::__construct($tokenStorage, $producer, $serializer);
    }

    public function countView(ViewableEntity $viewable): void
    {
        try {
            parent::countView($viewable);
        } catch (AMQPIOException $exception) {
            // rabbit is not available, so just throw everything into the database and do not care about performance

            $view = ViewFactory::createView($viewable, $this->tokenStorage->getToken()->getUser());

            $this->viewStoragePersister->storeView($view);

            $this->registry->getManager()->flush();
        }
    }
}
