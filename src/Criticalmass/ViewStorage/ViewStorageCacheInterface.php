<?php

namespace AppBundle\Criticalmass\ViewStorage;

use AppBundle\EntityInterface\ViewableInterface;

interface ViewStorageCacheInterface
{
    public function countView(ViewableInterface $viewable);
}
