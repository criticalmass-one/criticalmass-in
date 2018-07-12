<?php

namespace AppBundle\Criticalmass\ViewStorage;

interface ViewStoragePersisterInterface
{
    public function persistViews(): ViewStoragePersisterInterface;
}

