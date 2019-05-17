<?php declare(strict_types=1);

namespace App\Criticalmass\ViewStorage\Cache;

use App\EntityInterface\ViewableInterface;

interface ViewStorageCacheInterface
{
    public function countView(ViewableInterface $viewable): void;
}
