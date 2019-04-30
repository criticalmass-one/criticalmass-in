<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage;

interface ViewStoragePersisterInterface
{
    public function persistViews(array $viewList): ViewStoragePersisterInterface;
}

