<?php declare(strict_types=1);

namespace App\Consumer;

use App\Criticalmass\ViewStorage\Persister\ViewStoragePersisterInterface;

abstract class AbstractViewConsumer
{
    public function __construct(protected ViewStoragePersisterInterface $viewStoragePersister)
    {
    }
}
