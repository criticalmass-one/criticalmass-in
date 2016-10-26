<?php

namespace Caldera\Bundle\CalderaBundle\ViewStorage;

use Caldera\Bundle\CalderaBundle\EntityInterface\ViewableInterface;
use Doctrine\Common\Cache\MemcachedCache;
use Memcached;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ViewStorageCache implements ViewStorageCacheInterface
{
    /** @var MemcachedCache $cache */
    protected $cache;

    /** @var TokenStorageInterface $tokenStorage */
    protected $tokenStorage;

    public function __construct(MemcachedCache $cache, TokenStorageInterface $tokenStorage)
    {
        $this->cache = $cache;
        $this->tokenStorage = $tokenStorage;
    }

    public function countView(ViewableInterface $viewable)
    {
        /** @var Memcached $cache */
        $cache = $this->cache->getMemcached();

        $viewStorage = $cache->get('view_storage');

        if (!$viewStorage) {
            $viewStorage = [];
        }

        $viewDateTime = new \DateTime('now', new \DateTimeZone('UTC'));
        $user = $this->tokenStorage->getToken()->getUser();

        $view =
            [
                'className' => $this->getClassName($viewable),
                'entityId' => $viewable->getId(),
                'userId' => $user,
                'dateTime' => $viewDateTime->format('Y-m-d H:i:s')
            ];

        $viewStorage[] = $view;

        $cache->set('view_storage', $viewStorage);
    }

    protected function getClassName(ViewableInterface $viewable): string
    {
        $namespaceClass = get_class($viewable);
        $namespaceParts = explode('\\', $namespaceClass);

        $className = array_pop($namespaceParts);

        return $className;
    }
}