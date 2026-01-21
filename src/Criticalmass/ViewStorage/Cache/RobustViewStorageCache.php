<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\Cache;

use App\Criticalmass\ViewStorage\Persister\ViewStoragePersisterInterface;
use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use App\Criticalmass\ViewStorage\ViewModel\ViewFactory;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RobustViewStorageCache extends ViewStorageCache
{
    public function __construct(
        private readonly ViewStoragePersisterInterface $viewStoragePersister,
        TokenStorageInterface $tokenStorage,
        MessageBusInterface $messageBus,
    ) {
        parent::__construct($tokenStorage, $messageBus);
    }

    public function countView(ViewableEntity $viewable): void
    {
        try {
            parent::countView($viewable);
        } catch (TransportException) {
            // message transport is not available, so just throw everything into the database
            $view = ViewFactory::createView($viewable, $this->getUser());

            $this->viewStoragePersister->storeView($view);
        }
    }
}
