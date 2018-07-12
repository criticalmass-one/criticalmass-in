<?php

namespace App\Criticalmass\ViewStorage;

use App\EntityInterface\ViewableInterface;

interface ViewStorageCacheInterface
{
    public function countView(ViewableInterface $viewable);
}
