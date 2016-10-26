<?php

namespace Caldera\Bundle\CalderaBundle\ViewStorage;

use Caldera\Bundle\CalderaBundle\Entity\User;
use Caldera\Bundle\CalderaBundle\EntityInterface\ViewableInterface;
use Caldera\Bundle\CalderaBundle\EntityInterface\ViewInterface;
use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\ORM\EntityManager;
use Memcached;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

interface ViewStoragePersisterInterface
{
    public function persistViews(): ViewStoragePersisterInterface;
}

