<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\Cache;

use App\Criticalmass\ViewStorage\ViewInterface\ViewableEntity;

interface ViewStorageCacheInterface
{
    public function countView(ViewableEntity $viewable): void;
}
