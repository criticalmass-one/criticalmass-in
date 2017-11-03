<?php

namespace Criticalmass\Bundle\AppBundle\ViewStorage;

use Criticalmass\Bundle\AppBundle\EntityInterface\ViewableInterface;

interface ViewStorageCacheInterface
{
    public function countView(ViewableInterface $viewable);
}
