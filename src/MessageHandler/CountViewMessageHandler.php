<?php declare(strict_types=1);

namespace App\MessageHandler;

use App\Criticalmass\ViewStorage\Persister\ViewStoragePersisterInterface;
use App\Criticalmass\ViewStorage\ViewModel\View;
use App\Message\CountViewMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CountViewMessageHandler
{
    public function __construct(
        private readonly ViewStoragePersisterInterface $viewStoragePersister
    ) {
    }

    public function __invoke(CountViewMessage $message): void
    {
        $view = new View();
        $view
            ->setEntityId($message->getEntityId())
            ->setEntityClassName($message->getEntityClassName())
            ->setUserId($message->getUserId())
            ->setDateTime(\DateTime::createFromInterface($message->getDateTime()));

        $this->viewStoragePersister->storeView($view, true);
    }
}
