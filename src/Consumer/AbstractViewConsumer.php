<?php declare(strict_types=1);

namespace App\Consumer;

use App\Criticalmass\ViewStorage\Persister\ViewStoragePersisterInterface;

abstract class AbstractViewConsumer
{
    /** @var ViewStoragePersisterInterface $viewSotragePersister */
    protected $viewStoragePersister;

    public function __construct(ViewStoragePersisterInterface $viewStoragePersister)
    {
        $this->viewStoragePersister = $viewStoragePersister;
    }
}
