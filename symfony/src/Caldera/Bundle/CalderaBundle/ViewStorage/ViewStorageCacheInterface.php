<?php

namespace Caldera\Bundle\CalderaBundle\ViewStorage;

use Caldera\Bundle\CalderaBundle\EntityInterface\ViewableInterface;

interface ViewStorageCacheInterface
{
    public function countView(ViewableInterface $viewable);
}