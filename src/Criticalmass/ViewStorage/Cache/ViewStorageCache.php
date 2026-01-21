<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\Cache;

use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use App\Criticalmass\ViewStorage\ViewModel\ViewFactory;
use App\Entity\User;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ViewStorageCache implements ViewStorageCacheInterface
{
    public function __construct(
        protected readonly TokenStorageInterface $tokenStorage,
        protected readonly MessageBusInterface $messageBus,
    ) {

    }

    public function countView(ViewableEntity $viewable): void
    {
        $view = ViewFactory::createView($viewable, $this->getUser());

        $this->messageBus->dispatch($view);
    }

    protected function getUser(): ?User
    {
        $user = null;

        if ($this->tokenStorage->getToken()) {
            $user = $this->tokenStorage->getToken()->getUser();
        }

        return $user;
    }
}
