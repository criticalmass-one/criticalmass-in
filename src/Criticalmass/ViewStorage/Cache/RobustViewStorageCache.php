<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\Cache;

use App\Criticalmass\Util\ClassUtil;
use App\Criticalmass\ViewStorage\Persister\ViewStoragePersisterInterface;
use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;
use App\Criticalmass\ViewStorage\ViewModel\View;
use App\Entity\User;
use App\Message\CountViewMessage;
use Carbon\Carbon;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RobustViewStorageCache extends ViewStorageCache
{
    public function __construct(
        private readonly ViewStoragePersisterInterface $viewStoragePersister,
        TokenStorageInterface $tokenStorage,
        MessageBusInterface $messageBus
    ) {
        parent::__construct($tokenStorage, $messageBus);
    }

    public function countView(ViewableEntity $viewable): void
    {
        try {
            parent::countView($viewable);
        } catch (ExceptionInterface $exception) {
            // Messenger is not available, so just throw everything into the database directly
            $user = $this->getUser();

            $view = new View();
            $view
                ->setEntityId($viewable->getId())
                ->setEntityClassName(ClassUtil::getShortname($viewable))
                ->setUserId($user?->getId())
                ->setDateTime(Carbon::now('UTC'));

            $this->viewStoragePersister->storeView($view, true);
        }
    }
}
