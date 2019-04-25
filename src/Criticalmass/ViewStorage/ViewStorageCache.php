<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage;

use App\Criticalmass\Util\ClassUtil;
use App\Entity\User;
use App\EntityInterface\ViewableInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
        $userId = null;

        if ($user instanceof User) {
            $userId = $user->getId();
        }

        $view = [
            'className' => ClassUtil::getShortname($viewable),
            'entityId' => $viewable->getId(),
            'userId' => $userId,
            'dateTime' => $viewDateTime->format('Y-m-d H:i:s'),
        ];

        $this->producer->publish(serialize($view));
    }
}
