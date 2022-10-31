<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\EventSubscriber;

use App\Criticalmass\ViewStorage\BlackList\BlackListInterface;
use App\Criticalmass\ViewStorage\Cache\ViewStorageCacheInterface;
use App\Event\View\ViewEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ViewEventSubscriber implements EventSubscriberInterface
{
    public function __construct(protected ViewStorageCacheInterface $viewStorageCache, protected BlackListInterface $blackList)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ViewEvent::NAME => 'onView',
        ];
    }

    public function onView(ViewEvent $viewEvent): void
    {
        if (!$this->blackList->isBlackListed()) {
            $this->viewStorageCache->countView($viewEvent->getViewable());
        }
    }
}
