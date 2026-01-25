<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\Cache;

use App\Criticalmass\Util\ClassUtil;
use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use App\Entity\User;
use App\Message\CountViewMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ViewStorageCache implements ViewStorageCacheInterface
{
    public function __construct(
        protected TokenStorageInterface $tokenStorage,
        protected MessageBusInterface $messageBus
    ) {
    }

    public function countView(ViewableEntity $viewable): void
    {
        $user = $this->getUser();

        $message = new CountViewMessage(
            entityId: $viewable->getId(),
            entityClassName: ClassUtil::getShortname($viewable),
            userId: $user?->getId(),
            dateTime: new \DateTime('now', new \DateTimeZone('UTC'))
        );

        $this->messageBus->dispatch($message);
    }

    protected function getUser(): ?User
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return null;
        }

        $user = $token->getUser();

        return $user instanceof User ? $user : null;
    }
}
