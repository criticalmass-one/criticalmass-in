<?php declare(strict_types=1);

namespace App\MessageHandler;

use App\Criticalmass\ViewStorage\Persister\ViewStoragePersisterInterface;
use App\Criticalmass\ViewStorage\ViewModel\View;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Component\Messenger\Handler\BatchHandlerInterface;
use Symfony\Component\Messenger\Handler\BatchHandlerTrait;

#[AsMessageHandler]
final class ViewMessageHandler implements BatchHandlerInterface
{
    use BatchHandlerTrait;

    private const int BATCH_SIZE = 100;

    public function __construct(
        private readonly ViewStoragePersisterInterface $viewStoragePersister
    ) {
    }

    public function __invoke(View $view, ?Acknowledger $ack = null): mixed
    {
        return $this->handle($view, $ack);
    }

    private function process(array $viewList): void
    {
        $this->viewStoragePersister->persistViews($viewList);
    }

    private function shouldFlush(): bool
    {
        return $this->getBatchSize() <= \count($this->jobs);
    }

    private function getBatchSize(): int
    {
        return self::BATCH_SIZE;
    }
}
