<?php

namespace Criticalmass\Bundle\AppBundle\ViewStorage;

use AppBundle\EntityInterface\ViewableInterface;

interface ViewStorageCacheInterface
{
    public function countView(ViewableInterface $viewable);
}
