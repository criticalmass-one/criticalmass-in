<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\Cache;

use App\Criticalmass\ViewStorage\Persister\ViewStoragePersisterInterface;
use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use App\Criticalmass\ViewStorage\ViewModel\ViewFactory;
use JMS\Serializer\SerializerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use PhpAmqpLib\Exception\AMQPIOException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RobustViewStorageCache extends ViewStorageCache
{
    public function __construct(
        private ManagerRegistry $registry,
        private ViewStoragePersisterInterface $viewStoragePersister,
        TokenStorageInterface $tokenStorage,

        protected readonly MessageBusInterface $producer,
    )
    {
        parent::__construct($tokenStorage, $producer, $serializer);
    }

    public function countView(ViewableEntity $viewable): void
    {
        try {
            parent::countView($viewable);
        } catch (AMQPIOException $exception) {
            // rabbit is not available, so just throw everything into the database and do not care about performance

            $view = ViewFactory::createView($viewable, $this->getUser());

            $this->viewStoragePersister->storeView($view);

            //$this->registry->getManager()->flush();
        }
    }
}
