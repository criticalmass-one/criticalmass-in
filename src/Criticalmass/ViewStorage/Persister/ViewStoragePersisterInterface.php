<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\Persister;

use App\Criticalmass\ViewStorage\ViewModel\View;

interface ViewStoragePersisterInterface
{
    public function persistViews(array $viewList): ViewStoragePersisterInterface;
    public function storeView(View $view): ViewStoragePersisterInterface;
    public function setEntityNamespace(string $entityNamespace): ViewStoragePersisterInterface;
}

