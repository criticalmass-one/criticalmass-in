<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\Cache;

use App\Criticalmass\Util\ClassUtil;
use App\Criticalmass\ViewStorage\View\View;
use App\Criticalmass\ViewStorage\View\ViewFactory;
use App\EntityInterface\ViewableInterface;
use JMS\Serializer\SerializerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ViewStorageCache implements ViewStorageCacheInterface
{
    /** @var ProducerInterface $producer */
    protected $producer;

    /** @var TokenStorageInterface $tokenStorage */
    protected $tokenStorage;

    /** @var SerializerInterface $serializer */
    protected $serializer;

    public function __construct(TokenStorageInterface $tokenStorage, ProducerInterface $producer, SerializerInterface $serializer)
    {
        $this->producer = $producer;
        $this->tokenStorage = $tokenStorage;
        $this->serializer = $serializer;
    }

    public function countView(ViewableInterface $viewable): void
    {
        $view = ViewFactory::createView($viewable, $this->tokenStorage->getToken()->getUser());

        $this->producer->publish($this->serializer->serialize($view, 'json'));
    }
}
