<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage;

use App\Criticalmass\Util\ClassUtil;
use App\Entity\User;
use App\EntityInterface\ViewableInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ViewStorageCache implements ViewStorageCacheInterface
{
    /** @var ProducerInterface $producer */
    protected $producer;

    /** @var TokenStorageInterface $tokenStorage */
    protected $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage, ProducerInterface $producer)
    {
        $this->producer = $producer;

        $this->tokenStorage = $tokenStorage;
    }

    public function countView(ViewableInterface $viewable): void
    {
        $viewDateTime = new \DateTime('now', new \DateTimeZone('UTC'));

        $user = $this->tokenStorage->getToken()->getUser();

        $view = new View();
        $view
            ->setEntityClassName(ClassUtil::getShortname($viewable))
            ->setEntityId($viewable->getId())
            ->setUser($user instanceof UserInterface ? $user : null)
            ->setDateTime($viewDateTime);

        $this->producer->publish(serialize($view));
    }
}
