<?php

namespace Criticalmass\Bundle\AppBundle\Criticalmass\ViewStorage;

interface ViewStoragePersisterInterface
{
    public function persistViews(): ViewStoragePersisterInterface;
}

