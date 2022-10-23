<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\Cache;

use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use App\Criticalmass\ViewStorage\ViewModel\ViewFactory;
use JMS\Serializer\SerializerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ViewStorageCache implements ViewStorageCacheInterface
{
    public function __construct(protected TokenStorageInterface $tokenStorage, protected ProducerInterface $producer, protected SerializerInterface $serializer)
    {
    }

    public function countView(ViewableEntity $viewable): void
    {
        $view = ViewFactory::createView($viewable, $this->tokenStorage->getToken()->getUser());

        $this->producer->publish($this->serializer->serialize($view, 'json'));
    }
}
