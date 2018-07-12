<?php

namespace App\Criticalmass\ViewStorage;

interface ViewStoragePersisterInterface
{
    public function persistViews(): ViewStoragePersisterInterface;
}

